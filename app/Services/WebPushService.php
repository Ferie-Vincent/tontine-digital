<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushService
{
    private WebPush $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject' => config('webpush.vapid.subject'),
                'publicKey' => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ];

        $this->webPush = new WebPush($auth);
        $this->webPush->setReuseVAPIDHeaders(true);
    }

    public function sendToUser(int $userId, array $payload): void
    {
        $subscriptions = PushSubscription::where('user_id', $userId)->get();

        foreach ($subscriptions as $sub) {
            $this->queueNotification($sub, $payload);
        }

        $this->flush();
    }

    public function sendToMany(array $userIds, array $payload): void
    {
        if (empty($userIds)) {
            return;
        }

        $subscriptions = PushSubscription::whereIn('user_id', $userIds)->get();

        foreach ($subscriptions as $sub) {
            $this->queueNotification($sub, $payload);
        }

        $this->flush();
    }

    private function queueNotification(PushSubscription $sub, array $payload): void
    {
        $subscription = Subscription::create([
            'endpoint' => $sub->endpoint,
            'publicKey' => $sub->p256dh_key,
            'authToken' => $sub->auth_token,
            'contentEncoding' => $sub->content_encoding,
        ]);

        $this->webPush->queueNotification($subscription, json_encode($payload));
    }

    private function flush(): void
    {
        $expiredEndpoints = [];

        foreach ($this->webPush->flush() as $report) {
            if ($report->isSubscriptionExpired()) {
                $expiredEndpoints[] = $report->getEndpoint();
            }
        }

        if (!empty($expiredEndpoints)) {
            PushSubscription::whereIn('endpoint', $expiredEndpoints)->delete();
        }
    }
}
