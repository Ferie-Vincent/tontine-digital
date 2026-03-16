<?php

namespace App\Policies;

use App\Models\Tour;
use App\Models\User;

class TourPolicy
{
    /**
     * L'utilisateur peut gérer le tour (admin/trésorier de la tontine ou admin système).
     */
    public function manage(User $user, Tour $tour): bool
    {
        return $user->canManage($tour->tontine) || $user->is_admin;
    }

    /**
     * L'utilisateur peut confirmer la réception des fonds (bénéficiaire uniquement).
     */
    public function confirmReceipt(User $user, Tour $tour): bool
    {
        return $user->id === $tour->beneficiary_id;
    }
}
