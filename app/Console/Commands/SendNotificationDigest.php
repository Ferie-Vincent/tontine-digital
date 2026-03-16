<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNotificationDigest extends Command
{
    protected $signature = 'tontine:send-digest';
    protected $description = 'Envoie un résumé des notifications non lues aux utilisateurs ayant activé le digest';

    public function handle(): int
    {
        $totalSent = 0;

        // Digest quotidien
        $dailyUsers = User::where('notification_digest', 'daily')
            ->where('status', 'active')
            ->whereNotNull('email')
            ->get();

        foreach ($dailyUsers as $user) {
            $sent = $this->sendDigestForUser($user, now()->subDay());
            if ($sent) $totalSent++;
        }

        // Digest hebdomadaire (uniquement le lundi)
        if (now()->isMonday()) {
            $weeklyUsers = User::where('notification_digest', 'weekly')
                ->where('status', 'active')
                ->whereNotNull('email')
                ->get();

            foreach ($weeklyUsers as $user) {
                $sent = $this->sendDigestForUser($user, now()->subWeek());
                if ($sent) $totalSent++;
            }
        }

        $this->info("Digests envoyés : {$totalSent}.");
        return self::SUCCESS;
    }

    private function sendDigestForUser(User $user, $since): bool
    {
        $notifications = Notification::where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($notifications->isEmpty()) {
            return false;
        }

        // Grouper par tontine
        $grouped = $notifications->groupBy(function ($n) {
            return $n->data['tontine_id'] ?? 'general';
        });

        try {
            Mail::send('emails.notification-digest', [
                'user' => $user,
                'grouped' => $grouped,
                'totalCount' => $notifications->count(),
                'period' => $user->notification_digest === 'daily' ? 'aujourd\'hui' : 'cette semaine',
            ], function ($message) use ($user, $notifications) {
                $message->to($user->email)
                    ->subject('DIGI-TONTINE CI — ' . $notifications->count() . ' notification(s) en attente');
            });

            return true;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Digest email failed for user ' . $user->id . ': ' . $e->getMessage());
            return false;
        }
    }
}
