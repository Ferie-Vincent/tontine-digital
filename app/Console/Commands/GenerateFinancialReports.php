<?php

namespace App\Console\Commands;

use App\Enums\TontineStatus;
use App\Models\ActivityLog;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Services\NotificationService;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GenerateFinancialReports extends Command
{
    protected $signature = 'tontine:generate-reports';

    protected $description = 'Génère et envoie les rapports financiers périodiques aux managers et membres';

    public function handle(NotificationService $notificationService, SmsService $smsService): int
    {
        $tontines = Tontine::where('status', TontineStatus::ACTIVE)->get();
        $totalReports = 0;

        foreach ($tontines as $tontine) {
            if (!$tontine->getSetting('auto_reports_enabled', false)) {
                continue;
            }

            $frequency = $tontine->getSetting('report_frequency', 'weekly');
            $sendToMembers = $tontine->getSetting('report_send_to_members', false);

            // Vérifier si c'est le bon jour pour générer
            if (!$this->shouldGenerate($frequency)) {
                continue;
            }

            $reportData = $this->buildReportData($tontine, $frequency);

            // Générer le PDF
            $pdf = Pdf::loadView('reports.financial', [
                'tontine' => $tontine,
                'data' => $reportData,
                'generatedAt' => now(),
            ]);

            $fileName = "rapport-{$tontine->id}-" . now()->format('Y-m-d') . '.pdf';
            $filePath = "reports/{$fileName}";
            Storage::disk('public')->put($filePath, $pdf->output());

            // Notifier les managers
            $notificationService->notifyTontineManagers(
                $tontine,
                'financial_report',
                'Rapport financier — ' . $tontine->name,
                "Le rapport financier {$this->frequencyLabel($frequency)} de {$tontine->name} est disponible.\n"
                    . "Période : {$reportData['period_label']}\n"
                    . "Collecté : " . format_amount($reportData['total_collected']) . "\n"
                    . "Décaissé : " . format_amount($reportData['total_disbursed']) . "\n"
                    . "Tours complétés : {$reportData['completed_tours']}/{$reportData['total_tours']}",
                ['tontine_id' => $tontine->id, 'report_path' => $filePath],
                sendEmail: true
            );

            // Notifier tous les membres si configuré
            if ($sendToMembers) {
                $notificationService->notifyTontineMembers(
                    $tontine,
                    'financial_report_member',
                    'Rapport financier disponible',
                    "Le rapport {$this->frequencyLabel($frequency)} de {$tontine->name} est disponible. "
                        . "Progression : {$reportData['progress']}%.",
                    ['tontine_id' => $tontine->id]
                );

                // SMS aux membres si le service est actif
                if ($smsService->isEnabled()) {
                    $memberPhones = $tontine->activeMembers()
                        ->with('user')
                        ->get()
                        ->pluck('user.phone')
                        ->filter()
                        ->toArray();

                    $smsMessage = "TONTINE {$tontine->name}: Rapport {$this->frequencyLabel($frequency)} dispo. "
                        . "Collecte: " . format_amount($reportData['total_collected']) . ". "
                        . "Progression: {$reportData['progress']}%.";

                    $smsService->sendToMany($memberPhones, $smsMessage);
                }
            }

            $totalReports++;

            ActivityLog::log('financial_report_generated', $tontine, tontineId: $tontine->id, properties: [
                'frequency' => $frequency,
                'file_path' => $filePath,
                'period' => $reportData['period_label'],
            ]);
        }

        $this->info("Rapports financiers générés : {$totalReports}.");

        return self::SUCCESS;
    }

    private function shouldGenerate(string $frequency): bool
    {
        return match ($frequency) {
            'weekly' => now()->isMonday(),
            'biweekly' => now()->isMonday() && now()->weekOfYear % 2 === 0,
            'monthly' => now()->day === 1,
            default => false,
        };
    }

    private function buildReportData(Tontine $tontine, string $frequency): array
    {
        $periodStart = match ($frequency) {
            'weekly' => now()->subWeek()->startOfWeek(),
            'biweekly' => now()->subWeeks(2)->startOfWeek(),
            'monthly' => now()->subMonth()->startOfMonth(),
            default => now()->subWeek(),
        };
        $periodEnd = now();

        $periodLabel = $periodStart->format('d/m/Y') . ' au ' . $periodEnd->format('d/m/Y');

        // Statistiques de la période
        $periodContributions = Contribution::where('tontine_id', $tontine->id)
            ->where('status', 'confirmed')
            ->whereBetween('confirmed_at', [$periodStart, $periodEnd]);

        $periodCollected = (clone $periodContributions)->sum('amount');

        $periodDisbursed = $tontine->tours()
            ->whereNotNull('disbursed_at')
            ->whereBetween('disbursed_at', [$periodStart, $periodEnd])
            ->sum('collected_amount');

        // Statistiques globales (combinées en 2 requêtes au lieu de 5)
        $contributionStats = DB::table('contributions')
            ->where('tontine_id', $tontine->id)
            ->selectRaw("
                SUM(CASE WHEN status = 'confirmed' THEN amount ELSE 0 END) as total_collected,
                SUM(penalty_amount) as total_penalties
            ")
            ->first();

        $totalCollected = $contributionStats->total_collected ?? 0;
        $totalPenalties = $contributionStats->total_penalties ?? 0;

        $tourStats = DB::table('tours')
            ->where('tontine_id', $tontine->id)
            ->selectRaw("
                COUNT(*) as total_tours,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_tours,
                SUM(CASE WHEN disbursed_at IS NOT NULL THEN collected_amount ELSE 0 END) as total_disbursed
            ")
            ->first();

        $totalDisbursed = $tourStats->total_disbursed ?? 0;
        $completedTours = $tourStats->completed_tours ?? 0;
        $totalTours = $tourStats->total_tours ?? 0;
        $progress = $totalTours > 0 ? round(($completedTours / $totalTours) * 100) : 0;

        // Membres en retard
        $lateMembers = Contribution::where('tontine_id', $tontine->id)
            ->where('status', 'late')
            ->with('user')
            ->get()
            ->groupBy('user_id')
            ->map(fn($contributions) => [
                'name' => $contributions->first()->user?->name ?? 'Inconnu',
                'late_count' => $contributions->count(),
                'total_penalty' => $contributions->sum('penalty_amount'),
            ])
            ->values()
            ->toArray();

        // Prochains tours
        $upcomingTours = $tontine->tours()
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->with('beneficiary')
            ->orderBy('due_date')
            ->take(3)
            ->get()
            ->map(fn($tour) => [
                'number' => $tour->tour_number,
                'beneficiary' => $tour->beneficiary?->name ?? 'N/A',
                'due_date' => $tour->due_date->format('d/m/Y'),
                'status' => $tour->status->label(),
            ])
            ->toArray();

        return [
            'period_label' => $periodLabel,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'period_collected' => $periodCollected,
            'period_disbursed' => $periodDisbursed,
            'total_collected' => $totalCollected,
            'total_disbursed' => $totalDisbursed,
            'total_penalties' => $totalPenalties,
            'completed_tours' => $completedTours,
            'total_tours' => $totalTours,
            'progress' => $progress,
            'late_members' => $lateMembers,
            'upcoming_tours' => $upcomingTours,
            'active_members_count' => $tontine->activeMembers()->count(),
        ];
    }

    private function frequencyLabel(string $frequency): string
    {
        return match ($frequency) {
            'weekly' => 'hebdomadaire',
            'biweekly' => 'bimensuel',
            'monthly' => 'mensuel',
            default => $frequency,
        };
    }
}
