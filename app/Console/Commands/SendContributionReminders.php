<?php

namespace App\Console\Commands;

use App\Enums\ContributionStatus;
use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Models\ActivityLog;
use App\Models\Tontine;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendContributionReminders extends Command
{
    protected $signature = 'tontine:send-reminders';

    protected $description = 'Envoie des rappels de cotisation aux membres selon les paramètres de chaque tontine';

    public function handle(NotificationService $notificationService): int
    {
        $pausedCount = Tontine::where('status', TontineStatus::PAUSED)->count();
        if ($pausedCount > 0) {
            $this->info("{$pausedCount} tontine(s) en pause ignorée(s).");
        }

        $tontines = Tontine::where('status', TontineStatus::ACTIVE)
            ->where('status', '!=', TontineStatus::PAUSED)
            ->get();
        $totalSent = 0;

        foreach ($tontines as $tontine) {
            $reminderDays = $tontine->getSetting('reminder_days_before', [3, 1, 0]);

            if (empty($reminderDays)) {
                continue;
            }

            $ongoingTours = $tontine->tours()
                ->where('status', TourStatus::ONGOING)
                ->whereNotNull('due_date')
                ->get();

            foreach ($ongoingTours as $tour) {
                $daysUntilDue = now()->startOfDay()->diffInDays($tour->due_date->startOfDay(), false);

                if (!in_array((int) $daysUntilDue, $reminderDays)) {
                    continue;
                }

                // Membres avec contributions PENDING (pas encore déclaré)
                $pendingUserIds = $tour->contributions()
                    ->where('status', ContributionStatus::PENDING)
                    ->pluck('user_id')
                    ->toArray();

                if (empty($pendingUserIds)) {
                    continue;
                }

                $dueLabel = $daysUntilDue === 0
                    ? "aujourd'hui"
                    : ($daysUntilDue === 1 ? 'demain' : "dans {$daysUntilDue} jours");

                // Différencier titre et contenu selon l'urgence
                $isUrgent = $daysUntilDue <= 1;
                $title = $isUrgent
                    ? ($daysUntilDue === 0 ? 'Échéance aujourd\'hui !' : 'Échéance demain !')
                    : 'Rappel de cotisation';

                $penaltyWarning = '';
                if ($isUrgent) {
                    $penaltyAmount = $tontine->getSetting('late_penalty_amount', 0);
                    if ($penaltyAmount > 0) {
                        $penaltyWarning = ' Pénalité de retard : ' . format_amount($penaltyAmount) . '.';
                    }
                }

                $content = "Rappel : votre cotisation de {$tontine->formatted_amount} pour le tour #{$tour->tour_number} de {$tontine->name} est due {$dueLabel}."
                    . ($isUrgent ? " Déclarez votre paiement pour éviter les pénalités.{$penaltyWarning}" : '');

                $notificationService->sendToMany(
                    $pendingUserIds,
                    'contribution_reminder',
                    $title,
                    $content,
                    ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
                    sendEmail: $isUrgent
                );

                $totalSent += count($pendingUserIds);

                ActivityLog::log('sent_reminder', $tour, tontineId: $tontine->id, properties: [
                    'days_until_due' => $daysUntilDue,
                    'members_notified' => count($pendingUserIds),
                ]);
            }
        }

        $this->info("Rappels envoyés : {$totalSent} notifications.");

        return self::SUCCESS;
    }
}
