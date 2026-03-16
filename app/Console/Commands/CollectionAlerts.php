<?php

namespace App\Console\Commands;

use App\Enums\ContributionStatus;
use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Models\ActivityLog;
use App\Models\Tontine;
use App\Services\NotificationService;
use App\Services\SmsService;
use Illuminate\Console\Command;

class CollectionAlerts extends Command
{
    protected $signature = 'tontine:collection-alerts';

    protected $description = 'Envoie des alertes proactives aux managers quand la collecte est en danger';

    public function handle(NotificationService $notificationService, SmsService $smsService): int
    {
        $pausedCount = Tontine::where('status', TontineStatus::PAUSED)->count();
        if ($pausedCount > 0) {
            $this->info("{$pausedCount} tontine(s) en pause ignorée(s).");
        }

        $tontines = Tontine::where('status', TontineStatus::ACTIVE)
            ->where('status', '!=', TontineStatus::PAUSED)
            ->get();
        $totalAlerts = 0;

        foreach ($tontines as $tontine) {
            if (!$tontine->getSetting('collection_alerts_enabled', false)) {
                continue;
            }

            $alertThresholds = $tontine->getSetting('collection_alert_thresholds', [
                ['days_before' => 5, 'min_percent' => 30],
                ['days_before' => 3, 'min_percent' => 50],
                ['days_before' => 1, 'min_percent' => 80],
            ]);

            $ongoingTours = $tontine->tours()
                ->where('status', TourStatus::ONGOING)
                ->whereNotNull('due_date')
                ->get();

            foreach ($ongoingTours as $tour) {
                $daysUntilDue = (int) now()->startOfDay()->diffInDays($tour->due_date->startOfDay(), false);

                if ($daysUntilDue < 0) {
                    continue; // Tour déjà en retard, géré par MarkLateContributions
                }

                // Trouver le seuil applicable pour le nombre de jours restants
                $applicableThreshold = null;
                foreach ($alertThresholds as $threshold) {
                    if ($daysUntilDue <= $threshold['days_before']) {
                        $applicableThreshold = $threshold;
                        break;
                    }
                }

                if (!$applicableThreshold) {
                    continue;
                }

                // Calculer le pourcentage de collecte actuel
                $confirmedAmount = $tour->contributions()
                    ->where('status', ContributionStatus::CONFIRMED)
                    ->sum('amount');
                $expectedAmount = $tour->expected_amount ?: 1;
                $collectionPercent = round(($confirmedAmount / $expectedAmount) * 100, 1);

                if ($collectionPercent >= $applicableThreshold['min_percent']) {
                    continue; // La collecte est suffisante
                }

                // Déterminer le niveau d'urgence
                $urgencyLevel = match (true) {
                    $daysUntilDue <= 1 => 'critique',
                    $daysUntilDue <= 3 => 'urgent',
                    default => 'attention',
                };

                // Identifier les membres en retard
                $allContributions = $tour->contributions()->with('user')->get();
                $pendingMembers = $allContributions->whereIn('status', [ContributionStatus::PENDING, ContributionStatus::DECLARED]);

                $pendingNames = $pendingMembers->map(fn($c) => $c->user?->name ?? 'Inconnu')->implode(', ');
                $pendingCount = $pendingMembers->count();
                $totalMembers = $allContributions->count();
                $paidCount = $totalMembers - $pendingCount;
                $amount = format_amount($confirmedAmount, false);
                $expected = format_amount($expectedAmount, false);

                $dueLabel = $daysUntilDue === 0 ? "aujourd'hui" : ($daysUntilDue === 1 ? 'demain' : "dans {$daysUntilDue} jours");

                $emoji = match ($urgencyLevel) {
                    'critique' => '[CRITIQUE]',
                    'urgent' => '[URGENT]',
                    default => '[ATTENTION]',
                };

                $message = "{$emoji} Tour #{$tour->tour_number} de {$tontine->name} — Échéance {$dueLabel}.\n"
                    . "Collecte : {$collectionPercent}% ({$amount} / {$expected} FCFA)\n"
                    . "Payé : {$paidCount}/{$totalMembers} membres\n"
                    . "En attente : {$pendingNames}";

                $notificationService->notifyTontineManagers(
                    $tontine,
                    'collection_alert',
                    "Alerte collecte — Tour #{$tour->tour_number}",
                    $message,
                    ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
                    sendEmail: true
                );

                // SMS aux managers si critique
                if ($urgencyLevel === 'critique' && $smsService->isEnabled()) {
                    $managerPhones = $tontine->members()
                        ->whereIn('role', ['admin', 'treasurer'])
                        ->where('status', 'active')
                        ->with('user')
                        ->get()
                        ->pluck('user.phone')
                        ->filter()
                        ->toArray();

                    $smsMessage = "TONTINE {$tontine->name}: Tour #{$tour->tour_number} critique! Collecte {$collectionPercent}% ({$paidCount}/{$totalMembers}). Echeance {$dueLabel}.";
                    $smsService->sendToMany($managerPhones, $smsMessage);
                }

                $totalAlerts++;

                ActivityLog::log('collection_alert_sent', $tour, tontineId: $tontine->id, properties: [
                    'collection_percent' => $collectionPercent,
                    'days_until_due' => $daysUntilDue,
                    'urgency' => $urgencyLevel,
                    'pending_count' => $pendingCount,
                ]);
            }
        }

        $this->info("Alertes de collecte envoyées : {$totalAlerts}.");

        return self::SUCCESS;
    }
}
