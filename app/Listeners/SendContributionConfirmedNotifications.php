<?php

namespace App\Listeners;

use App\Events\ContributionConfirmed;

class SendContributionConfirmedNotifications
{
    public function handle(ContributionConfirmed $event): void
    {
        // TODO: Move notification logic here from ContributionService::confirm()
        // This listener will eventually handle:
        // - Notification au membre (payment_validated)
        // - Notification de remboursement de pénalité (penalty_refunded)
        // - Message système dans le chat de la tontine
    }
}
