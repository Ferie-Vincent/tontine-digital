<?php

namespace App\Http\Middleware;

use App\Models\Tontine;
use Closure;
use Illuminate\Http\Request;

class TontineMemberMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $tontine = $request->route('tontine');

        if (!$tontine instanceof Tontine) {
            $tontine = Tontine::findOrFail($tontine);
        }

        $user = $request->user();

        if (!$user->isMemberOf($tontine) && !$user->is_admin) {
            abort(403, 'Vous n\'êtes pas membre de cette tontine.');
        }

        return $next($request);
    }
}
