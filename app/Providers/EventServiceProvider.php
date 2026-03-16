<?php

namespace App\Providers;

use App\Events\ContributionConfirmed;
use App\Events\ContributionDeclared;
use App\Events\TourCompleted;
use App\Listeners\SendContributionConfirmedNotifications;
use App\Listeners\SendTourCompletedNotifications;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Mapping des événements vers leurs listeners.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ContributionConfirmed::class => [
            SendContributionConfirmedNotifications::class,
        ],
        ContributionDeclared::class => [
            // Listeners à ajouter selon les besoins
        ],
        TourCompleted::class => [
            SendTourCompletedNotifications::class,
        ],
    ];
}
