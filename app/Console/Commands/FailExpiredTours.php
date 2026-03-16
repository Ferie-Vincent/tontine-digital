<?php

namespace App\Console\Commands;

use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Models\ActivityLog;
use App\Models\Tontine;
use App\Services\NotificationService;
use App\Services\StatusTransitionService;
use Illuminate\Console\Command;

class FailExpiredTours extends Command
{
    protected $signature = 'tontine:fail-expired';

    protected $description = 'Marque les tours expirés comme échoués si la collecte est insuffisante';

    public function handle(NotificationService $notificationService): int
    {
        $pausedCount = Tontine::where('status', TontineStatus::PAUSED)->count();
        if ($pausedCount > 0) {
            $this->info("{$pausedCount} tontine(s) en pause ignorée(s).");
        }

        $tontines = Tontine::where('status', TontineStatus::ACTIVE)
            ->where('status', '!=', TontineStatus::PAUSED)
            ->get();
        $totalFailed = 0;

        foreach ($tontines as $tontine) {
            if (!$tontine->getSetting('tour_failure_enabled', false)) {
                continue;
            }

            $graceDays = $tontine->getSetting('tour_failure_grace_days', 7);
            $minCollectionPercent = $tontine->getSetting('tour_failure_min_collection_percent', 50);

            $expiredTours = $tontine->tours()
                ->where('status', TourStatus::ONGOING)
                ->whereNotNull('due_date')
                ->where('due_date', '<=', now()->subDays($graceDays))
                ->get();

            foreach ($expiredTours as $tour) {
                $collectedAmount = $tour->contributions()->where('status', 'confirmed')->sum('amount');
                $expectedAmount = $tour->expected_amount ?: ($tontine->contribution_amount * $tontine->activeMembers()->count());

                $collectionPercent = $expectedAmount > 0
                    ? round(($collectedAmount / $expectedAmount) * 100, 1)
                    : 0;

                if ($collectionPercent >= $minCollectionPercent) {
                    continue;
                }

                if (!StatusTransitionService::canTransition('tour', 'ONGOING', 'FAILED')) {
                    $this->warn("Transition ONGOING → FAILED non autorisée pour tour #{$tour->id}");
                    continue;
                }

                $tour->update([
                    'status' => TourStatus::FAILED,
                    'collected_amount' => $collectedAmount,
                ]);

                $totalFailed++;

                $beneficiaryName = $tour->beneficiary ? $tour->beneficiary->name : 'N/A';

                $notificationService->notifyTontineMembers(
                    $tontine,
                    'tour_failed',
                    'Tour échoué',
                    "Le tour #{$tour->tour_number} de {$tontine->name} (bénéficiaire : {$beneficiaryName}) a échoué. Collecte : {$collectionPercent}% ({$tour->formatted_collected_amount} sur {$tour->formatted_expected_amount}). Le délai de grâce de {$graceDays} jours est dépassé.",
                    ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
                    sendEmail: true
                );

                ActivityLog::log('tour_failed', $tour, tontineId: $tontine->id, properties: [
                    'collected_amount' => $collectedAmount,
                    'expected_amount' => $expectedAmount,
                    'collection_percent' => $collectionPercent,
                    'grace_days' => $graceDays,
                ]);
            }
        }

        $this->info("Tours marqués en échec : {$totalFailed}.");

        return self::SUCCESS;
    }
}
