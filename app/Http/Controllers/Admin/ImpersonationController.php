<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    public function start(User $user)
    {
        // Empêcher l'impersonation d'un autre admin
        if ($user->is_admin) {
            return back()->with('error', 'Impossible de se connecter en tant qu\'un autre administrateur.');
        }

        // Sauvegarder l'ID admin original en session
        session()->put('impersonating_from', auth()->id());
        session()->put('impersonating_name', auth()->user()->name);

        // Log l'action
        ActivityLog::log('impersonate_start', $user, userId: auth()->id());

        // Se connecter en tant que l'utilisateur
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', "Vous êtes maintenant connecté en tant que {$user->name}.");
    }

    public function stop()
    {
        $adminId = session()->get('impersonating_from');

        if (!$adminId) {
            return redirect()->route('dashboard');
        }

        $admin = User::find($adminId);
        if (!$admin) {
            return redirect()->route('dashboard');
        }

        // Log l'action
        ActivityLog::log('impersonate_stop', auth()->user(), userId: $adminId);

        // Nettoyer la session
        session()->forget('impersonating_from');
        session()->forget('impersonating_name');

        // Revenir au compte admin
        auth()->login($admin);

        return redirect()->route('admin.dashboard')->with('success', 'Vous êtes revenu à votre compte administrateur.');
    }
}
