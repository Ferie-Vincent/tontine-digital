<?php

namespace App\Console\Commands;

use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Models\Tontine;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemindReceiptConfirmation extends Command
{
    protected $signature = 'tontine:remind-receipt-confirmation';
    protected $description = 'Rappelle aux bénéficiaires de confirmer la réception des fonds';

    public function handle(NotificationService $notificationService): int
    {
        $pausedCount = Tontine::where('status', TontineStatus::PAUSED)->count();
        if ($pausedCount > 0) {
            $this->info("{$pausedCount} tontine(s) en pause ignorée(s).");
        }

        $tontines = Tontine::where('status', TontineStatus::ACTIVE)
            ->where('status', '!=', TontineStatus::PAUSED)
            ->get();
        $sent = 0;

        foreach ($tontines as $tontine) {
            // Tours versés mais pas confirmés par le bénéficiaire
            $tours = $tontine->tours()
                ->where('status', TourStatus::ONGOING)
                ->whereNotNull('disbursed_at')
                ->whereNull('beneficiary_confirmed_at')
                ->with('beneficiary')
                ->get();

            foreach ($tours as $tour) {
                $daysSinceDisbursement = Carbon::parse($tour->disbursed_at)->diffInDays(now());

                // Rappel tous les 3 jours (jour 3, 6, 9, etc.)
                if ($daysSinceDisbursement > 0 && $daysSinceDisbursement % 3 === 0) {
                    if ($tour->beneficiary) {
                        $notificationService->send(
                            $tour->beneficiary->id,
                            'receipt_confirmation_reminder',
                            'Confirmez la réception des fonds',
                            "Vous avez reçu les fonds du tour #{$tour->tour_number} de \"{$tontine->name}\" il y a {$daysSinceDisbursement} jours. Merci de confirmer la réception.",
                            ['tontine_id' => $tontine->id, 'tour_id' => $tour->id]
                        );
                        $sent++;

                        $this->line("Rappel envoyé à {$tour->beneficiary->name} pour le tour #{$tour->tour_number} de {$tontine->name} ({$daysSinceDisbursement} jours).");
                    }
                }
            }
        }

        $this->info("{$sent} rappel(s) de confirmation de réception envoyé(s).");

        return self::SUCCESS;
    }
}
