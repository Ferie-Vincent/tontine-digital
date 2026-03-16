<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $users = $query->withCount('tontineMembers')->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'nullable|email|max:255|unique:users,email',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'email.unique' => 'Cet email est déjà utilisé.',
        ]);

        $phone = $request->input('phone');
        // Générer un mot de passe aléatoire de 8 caractères
        $defaultPassword = Str::random(8);

        $user = User::create([
            'name' => $request->input('name'),
            'phone' => $phone,
            'email' => $request->input('email'),
            'password' => Hash::make($defaultPassword),
            'status' => 'active',
            'must_change_password' => true,
        ]);

        // Envoyer le mot de passe par SMS
        try {
            app(\App\Services\SmsService::class)->send(
                $user->phone,
                "Bienvenue sur Tontine ! Votre mot de passe temporaire est : {$defaultPassword}. Vous devrez le changer à votre première connexion."
            );
        } catch (\Exception $e) {
            // Si l'envoi SMS échoue, on log mais on continue
            \Log::warning("Impossible d'envoyer le mot de passe par SMS à {$user->phone}: " . $e->getMessage());
        }

        return back()->with('success', "Membre {$user->name} créé avec succès. Le mot de passe a été envoyé par SMS au {$user->phone}.");
    }

    public function show(User $user)
    {
        $user->load(['createdTontines', 'tontines']);
        $contributionsTotal = $user->contributions()->where('status', 'confirmed')->sum('amount');

        return view('admin.users.show', compact('user', 'contributionsTotal'));
    }

    public function suspend(User $user)
    {
        if ($user->is_admin) {
            return back()->with('error', 'Impossible de suspendre un administrateur.');
        }

        $user->update(['status' => 'suspended']);

        return back()->with('success', $user->name . ' a été suspendu.');
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);

        return back()->with('success', $user->name . ' a été réactivé.');
    }

    public function unlock(User $user)
    {
        $user->update(['locked_until' => null]);
        LoginAttempt::clearFor($user->phone);

        return back()->with('success', 'Compte déverrouillé pour ' . $user->name . '.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,suspend,export',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $count = $users->count();

        switch ($request->action) {
            case 'activate':
                User::whereIn('id', $request->user_ids)->update(['status' => 'active']);
                ActivityLog::log('bulk_activate', null, userId: auth()->id(), properties: ['count' => $count]);
                return back()->with('success', "{$count} utilisateur(s) activé(s).");

            case 'suspend':
                $suspended = User::whereIn('id', $request->user_ids)
                    ->where('is_admin', false)
                    ->update(['status' => 'suspended']);
                ActivityLog::log('bulk_suspend', null, userId: auth()->id(), properties: ['count' => $suspended]);
                return back()->with('success', "{$suspended} utilisateur(s) suspendu(s).");

            case 'export':
                $filename = 'utilisateurs_' . date('Y-m-d_His') . '.csv';
                return response()->streamDownload(function () use ($users) {
                    $handle = fopen('php://output', 'w');
                    // BOM UTF-8 pour Excel
                    fwrite($handle, "\xEF\xBB\xBF");
                    fputcsv($handle, ['Nom', 'Téléphone', 'Email', 'Statut', 'Inscrit le'], ';');
                    foreach ($users as $user) {
                        fputcsv($handle, [
                            $user->name,
                            $user->phone,
                            $user->email ?? '',
                            $user->status === 'suspended' ? 'Suspendu' : 'Actif',
                            $user->created_at->format('d/m/Y'),
                        ], ';');
                    }
                    fclose($handle);
                }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }
    }
}