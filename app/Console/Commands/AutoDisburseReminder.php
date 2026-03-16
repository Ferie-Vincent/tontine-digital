<?php

namespace App\Console\Commands;

use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Models\ActivityLog;
use App\Models\Tontine;
use App\Services\NotificationService;
use App\Services\SmsService;
use Illuminate\Console\Command;

class AutoDisburseReminder extends Command
{
    protected $signature = 'tontine:auto-disburse';

    protected $description = 'Relance les managers pour les tours prêts à être décaissés et non encore versés';

    public function handle(NotificationService $notificationService, SmsService $smsService): int
    {
        $pausedCount = Tontine::where('status', TontineStatus::PAUSED)->count();
        if ($pausedCount > 0) {
            $this->info("{$pausedCount} tontine(s) en pause ignorée(s).");
        }

        $tontines = Tontine::where('status', TontineStatus::ACTIVE)
            ->where('status', '!=', TontineStatus::PAUSED)
            ->get();
        $totalReminders = 0;

        foreach ($tontines as $tontine) {
            if (!$tontine->getSetting('auto_disburse_reminder', false)) {
                continue;
            }

            $delayHours = $tontine->getSetting('disburse_reminder_delay_hours', 24);

            // Tours complétés (toutes contributions confirmées) mais non décaissés
            $readyTours = $tontine->tours()
                ->where('status', TourStatus::COMPLETED)
                ->whereNotNull('collection_date')
                ->whereNull('disbursed_at')
                ->get();

            foreach ($readyTours as $tour) {
                // Vérifier si le délai est dépassé
                $hoursWaiting = $tour->collection_date->diffInHours(now());

                if ($hoursWaiting < $delayHours) {
                    continue;
                }

                // Calculer l'urgence
                $urgencyLevel = match (true) {
                    $hoursWaiting >= $delayHours * 3 => 'critique',
                    $hoursWaiting >= $delayHours * 2 => 'urgent',
                    default => 'rappel',
                };

                $beneficiary = $tour->beneficiary;
                $amount = format_amount($tour->collected_amount);

                $message = match ($urgencyLevel) {
                    'critique' => "[CRITIQUE] Le tour #{$tour->tour_number} de {$tontine->name} attend le décaissement depuis " . round($hoursWaiting) . "h. {$amount} à verser à " . ($beneficiary?->name ?? 'N/A') . ".",
                    'urgent' => "[URGENT] Rappel : le tour #{$tour->tour_number} de {$tontine->name} est prêt pour décaissement depuis " . round($hoursWaiting) . "h. Montant : {$amount} pour " . ($beneficiary?->name ?? 'N/A') . ".",
                    default => "Rappel : le tour #{$tour->tour_number} de {$tontine->name} est prêt pour décaissement. Montant : {$amount} pour " . ($beneficiary?->name ?? 'N/A') . ".",
                };

                $notificationService->notifyTontineManagers(
                    $tontine,
                    'disburse_reminder',
                    'Rappel de décaissement',
                    $message,
                    ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
                    sendEmail: true
                );

                // Envoyer aussi par SMS aux managers si le service est actif
                if ($smsService->isEnabled()) {
                    $managerPhones = $tontine->members()
                        ->whereIn('role', ['admin', 'treasurer'])
                        ->where('status', 'active')
                        ->with('user')
                        ->get()
                        ->pluck('user.phone')
                        ->filter()
                        ->toArray();

                    if (!empty($managerPhones)) {
                        $smsService->sendToMany($managerPhones, $message);
                    }
                }

                $totalReminders++;

                ActivityLog::log('disburse_reminder_sent', $tour, tontineId: $tontine->id, properties: [
                    'hours_waiting' => round($hoursWaiting),
                    'urgency' => $urgencyLevel,
                ]);
            }
        }

        $this->info("Rappels de décaissement envoyés : {$totalReminders}.");

        return self::SUCCESS;
    }
}
