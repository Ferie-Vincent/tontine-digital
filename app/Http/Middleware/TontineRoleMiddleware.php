<?php

namespace App\Http\Middleware;

use App\Models\TontineMember;
use Closure;
use Illuminate\Http\Request;

class TontineRoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $tontine = $request->route('tontine');
        if (!$tontine) {
            abort(404);
        }

        $member = TontineMember::where('tontine_id', $tontine->id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (!$member || !in_array($member->role->value, $roles)) {
            abort(403, 'Vous n\'avez pas les droits nécessaires.');
        }

        return $next($request);
    }
}
