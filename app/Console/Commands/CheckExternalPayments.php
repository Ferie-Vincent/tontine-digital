<?php

namespace App\Console\Commands;

use App\Enums\ContributionStatus;
use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Models\ActivityLog;
use App\Models\Tontine;
use App\Services\ContributionService;
use App\Services\NotificationService;
use App\Services\PaymentGatewayService;
use Illuminate\Console\Command;

class CheckExternalPayments extends Command
{
    protected $signature = 'tontine:check-payments';

    protected $description = 'Vérifie les paiements reçus via les APIs des opérateurs et auto-confirme les contributions correspondantes';

    public function handle(
        PaymentGatewayService $gateway,
        NotificationService $notificationService,
        ContributionService $contributionService
    ): int {
        $providers = $gateway->getActiveProviders();

        if (empty($providers)) {
            $this->info('Aucun opérateur de paiement actif. Vérifiez les paramètres.');
            return self::SUCCESS;
        }

        $this->info('Opérateurs actifs : ' . implode(', ', $providers));

        // Récupérer toutes les contributions en attente (PENDING ou DECLARED)
        $tontines = Tontine::where('status', TontineStatus::ACTIVE)->get();
        $totalConfirmed = 0;
        $totalErrors = 0;

        foreach ($tontines as $tontine) {
            $ongoingTours = $tontine->tours()
                ->where('status', TourStatus::ONGOING)
                ->get();

            foreach ($ongoingTours as $tour) {
                $pendingModels = $tour->contributions()
                    ->with('user', 'paymentProof')
                    ->whereIn('status', [ContributionStatus::PENDING, ContributionStatus::DECLARED])
                    ->get();

                if ($pendingModels->isEmpty()) {
                    continue;
                }

                $results = $gateway->checkIncomingPayments($pendingModels->all());

                $totalConfirmed += $results['confirmed'];

                // Notifier et logger pour chaque confirmation
                if ($results['confirmed'] > 0) {
                    // Mettre à jour le montant collecté du tour
                    $collectedAmount = $tour->contributions()
                        ->where('status', ContributionStatus::CONFIRMED)
                        ->sum('amount');
                    $tour->update(['collected_amount' => $collectedAmount]);

                    ActivityLog::log('auto_confirmed_payments', $tour, tontineId: $tontine->id, properties: [
                        'confirmed_count' => $results['confirmed'],
                        'providers' => $providers,
                    ]);

                    // Vérifier si le tour peut être auto-complété
                    $remainingPending = $tour->contributions()
                        ->whereNot('status', ContributionStatus::CONFIRMED)
                        ->count();

                    if ($remainingPending === 0) {
                        $contributionService->autoCompleteTourIfFullyPaid(
                            $tour->contributions()->where('status', ContributionStatus::CONFIRMED)->first()
                        );
                    }

                    $notificationService->notifyTontineManagers(
                        $tontine,
                        'payments_auto_confirmed',
                        'Paiements auto-confirmés',
                        $results['confirmed'] . ' paiement(s) confirmé(s) automatiquement pour le tour #' . $tour->tour_number . '.',
                        ['tontine_id' => $tontine->id, 'tour_id' => $tour->id]
                    );
                }

                if (!empty($results['errors'])) {
                    $totalErrors += count($results['errors']);
                }
            }
        }

        $this->info("Paiements auto-confirmés : {$totalConfirmed}. Erreurs : {$totalErrors}.");

        return self::SUCCESS;
    }
}
