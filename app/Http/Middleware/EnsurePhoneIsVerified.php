<?php

namespace App\Http\Middleware;

use App\Models\SiteSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!SiteSettings::getBoolean('require_phone_verification')) {
            return $next($request);
        }

        if ($request->user() && !$request->user()->isPhoneVerified()) {
            return redirect()->route('phone.verify');
        }

        return $next($request);
    }
}
