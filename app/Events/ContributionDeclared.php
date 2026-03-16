<?php

namespace App\Events;

use App\Models\Contribution;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContributionDeclared
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Contribution $contribution,
        public int $declaredBy,
    ) {}
}
