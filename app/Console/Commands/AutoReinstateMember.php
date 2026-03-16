<?php

namespace App\Console\Commands;

use App\Enums\ContributionStatus;
use App\Enums\MemberStatus;
use App\Enums\TontineStatus;
use App\Models\ActivityLog;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Models\TontineMember;
use App\Services\NotificationService;
use App\Services\SmsService;
use Illuminate\Console\Command;

class AutoReinstateMember extends Command
{
    protected $signature = 'tontine:auto-reinstate';

    protected $description = 'Réintègre automatiquement les membres exclus qui ont rattrapé tous leurs retards';

    public function handle(NotificationService $notificationService, SmsService $smsService): int
    {
        $tontines = Tontine::where('status', TontineStatus::ACTIVE)->get();
        $totalReinstated = 0;

        foreach ($tontines as $tontine) {
            if (!$tontine->getSetting('auto_reinstate_enabled', false)) {
                continue;
            }

            $graceDays = $tontine->getSetting('reinstate_grace_days', 7);

            // Trouver les membres exclus
            $excludedMembers = $tontine->members()
                ->where('status', MemberStatus::EXCLUDED)
                ->get();

            foreach ($excludedMembers as $member) {
                // Vérifier si le membre a des contributions LATE non résolues
                $hasUnresolvedLate = Contribution::where('user_id', $member->user_id)
                    ->where('tontine_id', $tontine->id)
                    ->where('status', ContributionStatus::LATE)
                    ->exists();

                if ($hasUnresolvedLate) {
                    continue; // Encore des retards non rattrapés
                }

                // Vérifier le délai de carence depuis la dernière confirmation
                $lastConfirmed = Contribution::where('user_id', $member->user_id)
                    ->where('tontine_id', $tontine->id)
                    ->where('status', ContributionStatus::CONFIRMED)
                    ->latest('confirmed_at')
                    ->first();

                if (!$lastConfirmed || !$lastConfirmed->confirmed_at) {
                    continue; // Aucun paiement confirmé trouvé
                }

                $daysSinceLastPayment = $lastConfirmed->confirmed_at->diffInDays(now());

                if ($daysSinceLastPayment < $graceDays) {
                    continue; // Délai de carence non atteint
                }

                // Réintégrer le membre
                $member->update(['status' => MemberStatus::ACTIVE]);
                $totalReinstated++;

                ActivityLog::log('auto_reinstated', $member, userId: $member->user_id, tontineId: $tontine->id, properties: [
                    'grace_days' => $graceDays,
                    'days_since_last_payment' => $daysSinceLastPayment,
                ]);

                // Notifier le membre réintégré
                $notificationService->send(
                    $member->user_id,
                    'member_reinstated',
                    'Réintégration dans la tontine',
                    "Vous avez été automatiquement réintégré dans la tontine {$tontine->name} après avoir rattrapé vos paiements en retard. Bienvenue de retour !",
                    ['tontine_id' => $tontine->id],
                    sendEmail: true
                );

                // SMS si disponible
                if ($smsService->isEnabled() && $member->user?->phone) {
                    $smsService->send(
                        $member->user->phone,
                        "TONTINE {$tontine->name}: Vous avez ete reintegre automatiquement apres rattrapage de vos retards. Bienvenue!"
                    );
                }

                // Notifier les managers
                $notificationService->notifyTontineManagers(
                    $tontine,
                    'member_auto_reinstated',
                    'Membre réintégré automatiquement',
                    ($member->user?->name ?? 'Un membre') . " a été automatiquement réintégré dans {$tontine->name} après avoir rattrapé tous ses retards.",
                    ['tontine_id' => $tontine->id, 'user_id' => $member->user_id]
                );
            }
        }

        $this->info("Membres réintégrés automatiquement : {$totalReinstated}.");

        return self::SUCCESS;
    }
}
