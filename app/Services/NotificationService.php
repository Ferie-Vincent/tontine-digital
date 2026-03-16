<?php

namespace App\Services;

use App\Mail\TontineNotificationMail;
use App\Models\Notification;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    private ?WebPushService $webPush = null;

    private function webPush(): WebPushService
    {
        if (!$this->webPush) {
            $this->webPush = app(WebPushService::class);
        }
        return $this->webPush;
    }

    public function send(
        int $userId,
        string $type,
        string $title,
        string $content,
        ?array $data = null,
        bool $sendEmail = false
    ): Notification {
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'data' => $data,
            'channel' => 'database',
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Vérifier si l'utilisateur préfère le digest
        if ($sendEmail) {
            $user = User::find($userId);
            if ($user && $user->notification_digest !== 'instant') {
                $sendEmail = false; // Sera regroupé par la commande digest
            }
        }

        if ($sendEmail) {
            $this->sendEmail($userId, $title, $content, $data);
        }

        $this->sendPush([$userId], $title, $content, $data);

        return $notification;
    }

    public function sendToMany(
        array $userIds,
        string $type,
        string $title,
        string $content,
        ?array $data = null,
        bool $sendEmail = false
    ): void {
        if (empty($userIds)) {
            return;
        }

        $now = now();
        $notifications = [];

        foreach ($userIds as $userId) {
            $notifications[] = [
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'content' => $content,
                'data' => $data ? json_encode($data) : null,
                'channel' => 'database',
                'status' => 'sent',
                'sent_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Bulk insert par lots de 500 pour éviter les limites SQL
        foreach (array_chunk($notifications, 500) as $batch) {
            Notification::insert($batch);
        }

        if ($sendEmail) {
            // Charger tous les utilisateurs en une seule requête au lieu de N requêtes
            $users = User::whereIn('id', $userIds)
                ->where('notification_digest', 'instant')
                ->whereNotNull('email')
                ->get();

            foreach ($users as $user) {
                $this->sendEmailToUser($user, $title, $content, $data);
            }
        }

        $this->sendPush($userIds, $title, $content, $data);
    }

    public function notifyTontineMembers(
        Tontine $tontine,
        string $type,
        string $title,
        string $content,
        ?array $data = null,
        array $excludeUserIds = [],
        bool $sendEmail = false
    ): void {
        $userIds = $tontine->activeMembers()
            ->whereNotIn('user_id', $excludeUserIds)
            ->pluck('user_id')
            ->toArray();

        $this->sendToMany($userIds, $type, $title, $content, $data, $sendEmail);
    }

    public function notifyTontineManagers(
        Tontine $tontine,
        string $type,
        string $title,
        string $content,
        ?array $data = null,
        bool $sendEmail = false
    ): void {
        $userIds = $tontine->members()
            ->whereIn('role', ['admin', 'treasurer'])
            ->where('status', 'active')
            ->pluck('user_id')
            ->toArray();

        $this->sendToMany($userIds, $type, $title, $content, $data, $sendEmail);
    }

    public function notifyAdmins(
        string $type,
        string $title,
        string $content,
        ?array $data = null,
        bool $sendEmail = false
    ): void {
        $adminIds = User::where('is_admin', true)->pluck('id')->toArray();

        $this->sendToMany($adminIds, $type, $title, $content, $data, $sendEmail);
    }

    private function sendPush(array $userIds, string $title, string $content, ?array $data): void
    {
        if (empty($userIds) || empty(config('webpush.vapid.public_key'))) {
            return;
        }

        try {
            $payload = [
                'title' => $title,
                'body' => $content,
                'icon' => 'icons/icon-192x192.png',
                'url' => '/',
            ];

            if (isset($data['tontine_id'], $data['tour_id'])) {
                $payload['url'] = route('tontines.tours.show', [$data['tontine_id'], $data['tour_id']]);
            } elseif (isset($data['tontine_id'])) {
                $payload['url'] = route('tontines.show', $data['tontine_id']);
            }

            $this->webPush()->sendToMany($userIds, $payload);
        } catch (\Throwable $e) {
            // Push failure should not break the notification flow
            \Illuminate\Support\Facades\Log::warning('Push notification failed: ' . $e->getMessage());
        }
    }

    private function sendEmail(int $userId, string $title, string $content, ?array $data): void
    {
        $user = User::find($userId);
        if (!$user || !$user->email) {
            return;
        }

        $this->sendEmailToUser($user, $title, $content, $data);
    }

    private function sendEmailToUser(User $user, string $title, string $content, ?array $data): void
    {
        $actionUrl = null;
        if (isset($data['tontine_id']) && isset($data['tour_id'])) {
            $actionUrl = route('tontines.tours.show', [$data['tontine_id'], $data['tour_id']]);
        } elseif (isset($data['tontine_id'])) {
            $actionUrl = route('tontines.show', $data['tontine_id']);
        }

        Mail::to($user->email)->queue(
            new TontineNotificationMail($title, $content, $actionUrl, 'Voir la tontine')
        );
    }
}
