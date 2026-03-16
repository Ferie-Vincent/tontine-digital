<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        $sessions = auth()->user()->sessions()->orderByDesc('last_activity')->get();

        return view('settings.index', compact('sessions'));
    }

    public function destroySession(Request $request, UserSession $session)
    {
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        if ($session->session_id === session()->getId()) {
            return back()->with('error', 'Vous ne pouvez pas déconnecter votre session actuelle.');
        }

        // Supprimer le fichier de session si le driver est file
        if (config('session.driver') === 'file') {
            $sessionPath = config('session.files') . '/' . $session->session_id;
            if (File::exists($sessionPath)) {
                File::delete($sessionPath);
            }
        }

        // Supprimer la session de la table sessions si le driver est database
        if (config('session.driver') === 'database') {
            \DB::table(config('session.table', 'sessions'))
                ->where('id', $session->session_id)
                ->delete();
        }

        $session->delete();

        return back()->with('success', 'Session déconnectée avec succès.');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'email.email' => 'L\'email n\'est pas valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'avatar.image' => 'Le fichier doit être une image.',
            'avatar.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            unset($validated['avatar']);
        }

        $user->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function removeAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'Photo de profil supprimée.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères avec des lettres et des chiffres.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }

    public function updateNotifications(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'notify_contributions' => 'boolean',
            'notify_tours' => 'boolean',
            'notify_members' => 'boolean',
            'notification_digest' => 'in:instant,daily,weekly',
        ]);

        $user->update([
            'notification_preferences' => [
                'contributions' => $validated['notify_contributions'] ?? false,
                'tours' => $validated['notify_tours'] ?? false,
                'members' => $validated['notify_members'] ?? false,
            ],
            'notification_digest' => $validated['notification_digest'] ?? 'instant',
        ]);

        return back()->with('success', 'Préférences de notifications mises à jour.');
    }
}
