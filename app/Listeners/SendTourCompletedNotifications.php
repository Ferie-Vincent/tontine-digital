<?php

namespace App\Listeners;

use App\Events\TourCompleted;

class SendTourCompletedNotifications
{
    public function handle(TourCompleted $event): void
    {
        // TODO: Move notification logic here from ContributionService::autoCompleteTourIfFullyPaid()
        // This listener will eventually handle:
        // - Notification à tous les membres (all_contributions_confirmed)
        // - Notification spéciale au bénéficiaire (tour_ready_for_disbursement)
        // - Message système dans le chat de la tontine
    }
}
