<?php

namespace App\Services;

use App\Models\LoginAttempt;
use App\Models\User;

class LoginProtectionService
{
    /**
     * Verifier si le numero est actuellement verrouille.
     * Retourne [locked, message, seconds_remaining]
     */
    public function checkLock(string $phone): array
    {
        // Verifier le verrouillage au niveau utilisateur
        $user = User::where('phone', $phone)->first();
        if ($user && $user->locked_until && $user->locked_until->isFuture()) {
            $seconds = now()->diffInSeconds($user->locked_until);
            $minutes = ceil($seconds / 60);
            return [
                'locked' => true,
                'message' => "Compte verrouillé. Réessayez dans {$minutes} minute(s) ou contactez un administrateur.",
                'seconds' => $seconds,
            ];
        }

        // Verifier les verrouillages bases sur les tentatives
        $attemptsIn15Min = LoginAttempt::countRecent($phone, 15);
        if ($attemptsIn15Min >= 5) {
            return [
                'locked' => true,
                'message' => 'Trop de tentatives. Réessayez dans 15 minutes.',
                'seconds' => 900,
            ];
        }

        return ['locked' => false, 'message' => null, 'seconds' => 0];
    }

    /**
     * Enregistrer une tentative echouee et appliquer la protection progressive.
     */
    public function recordFailure(string $phone): void
    {
        LoginAttempt::record($phone);

        $attemptsIn15Min = LoginAttempt::countRecent($phone, 15);
        $attemptsIn1Hour = LoginAttempt::countRecent($phone, 60);
        $attemptsIn24Hours = LoginAttempt::countRecent($phone, 1440);

        $user = User::where('phone', $phone)->first();

        // Niveau 3 : 20+ tentatives en 24h -> verrouiller le compte pour 24h
        if ($attemptsIn24Hours >= 20 && $user) {
            $user->update(['locked_until' => now()->addHours(24)]);

            // Notifier les administrateurs
            app(NotificationService::class)->notifyAdmins(
                'account_locked',
                'Compte verrouillé automatiquement',
                "Le compte de {$user->name} ({$user->phone}) a été verrouillé après {$attemptsIn24Hours} tentatives de connexion échouées en 24h.",
                ['user_id' => $user->id]
            );

            // Notifier l'utilisateur par SMS
            $smsService = app(SmsService::class);
            if ($smsService->isEnabled()) {
                $smsService->send(
                    $phone,
                    'DIGI-TONTINE CI: Votre compte a ete verrouille apres de nombreuses tentatives de connexion. Contactez l\'administrateur si ce n\'est pas vous.'
                );
            }

            return;
        }

        // Niveau 2 : 10+ tentatives en 1h -> notifier les administrateurs
        if ($attemptsIn1Hour >= 10 && $attemptsIn1Hour < 12 && $user) {
            app(NotificationService::class)->notifyAdmins(
                'suspicious_login',
                'Tentatives de connexion suspectes',
                "{$attemptsIn1Hour} tentatives échouées pour le compte {$user->name} ({$user->phone}) dans la dernière heure.",
                ['user_id' => $user->id]
            );
        }
    }

    /**
     * Effacer les verrouillages et tentatives apres une connexion reussie.
     */
    public function clearOnSuccess(string $phone): void
    {
        LoginAttempt::clearFor($phone);

        $user = User::where('phone', $phone)->first();
        if ($user && $user->locked_until) {
            $user->update(['locked_until' => null]);
        }
    }
}
