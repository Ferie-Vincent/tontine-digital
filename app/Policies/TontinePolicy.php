<?php

namespace App\Policies;

use App\Models\Tontine;
use App\Models\User;

class TontinePolicy
{
    /**
     * L'utilisateur peut voir la tontine (membre ou admin système).
     */
    public function view(User $user, Tontine $tontine): bool
    {
        return $user->isMemberOf($tontine) || $user->is_admin;
    }

    /**
     * L'utilisateur peut gérer la tontine (admin/trésorier de la tontine ou admin système).
     */
    public function manage(User $user, Tontine $tontine): bool
    {
        return $user->canManage($tontine) || $user->is_admin;
    }

    /**
     * L'utilisateur est admin de la tontine (ou admin système).
     */
    public function administrate(User $user, Tontine $tontine): bool
    {
        return $user->isAdminOf($tontine) || $user->is_admin;
    }

    /**
     * L'utilisateur peut supprimer la tontine (créateur uniquement).
     */
    public function delete(User $user, Tontine $tontine): bool
    {
        return $user->id === $tontine->creator_id;
    }
}
