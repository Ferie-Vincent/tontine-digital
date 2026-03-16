<?php

namespace App\Services;

use App\Models\SiteSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Envoie un SMS à un numéro de téléphone.
     */
    public function send(string $phone, string $message): array
    {
        $provider = SiteSettings::get('sms_provider', 'disabled');

        if ($provider === 'disabled') {
            Log::info("SMS non envoyé (désactivé) : {$phone} - {$message}");
            return ['success' => false, 'error' => 'SMS désactivé'];
        }

        $phone = $this->normalizePhone($phone);

        return match ($provider) {
            'twilio' => $this->sendViaTwilio($phone, $message),
            'infobip' => $this->sendViaInfobip($phone, $message),
            'orange_sms' => $this->sendViaOrangeSms($phone, $message),
            'letexto' => $this->sendViaLetexto($phone, $message),
            default => ['success' => false, 'error' => "Fournisseur SMS inconnu : {$provider}"],
        };
    }

    /**
     * Envoie un SMS à plusieurs numéros.
     */
    public function sendToMany(array $phones, string $message): array
    {
        $results = ['sent' => 0, 'failed' => 0, 'errors' => []];

        foreach ($phones as $phone) {
            $result = $this->send($phone, $message);
            if ($result['success']) {
                $results['sent']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "{$phone}: " . ($result['error'] ?? 'Erreur inconnue');
            }
        }

        return $results;
    }

    /**
     * Vérifie si le service SMS est activé et configuré.
     */
    public function isEnabled(): bool
    {
        $provider = SiteSettings::get('sms_provider', 'disabled');
        return $provider !== 'disabled';
    }

    /**
     * Retourne le nom du fournisseur actif.
     */
    public function getActiveProvider(): string
    {
        return SiteSettings::get('sms_provider', 'disabled');
    }

    // ─────────────────────────────────────────────
    // Twilio
    // ─────────────────────────────────────────────

    private function sendViaTwilio(string $phone, string $message): array
    {
        $sid = SiteSettings::get('sms_twilio_sid');
        $token = SiteSettings::get('sms_twilio_token');
        $from = SiteSettings::get('sms_twilio_from');

        if (!$sid || !$token || !$from) {
            return ['success' => false, 'error' => 'Configuration Twilio incomplète'];
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To' => '+225' . $phone,
                    'Body' => $message,
                ]);

            return [
                'success' => $response->successful(),
                'message_id' => $response->json('sid'),
                'error' => $response->successful() ? null : $response->json('message', 'Erreur Twilio'),
            ];
        } catch (\Throwable $e) {
            Log::error('Twilio SMS error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // Infobip
    // ─────────────────────────────────────────────

    private function sendViaInfobip(string $phone, string $message): array
    {
        $apiKey = SiteSettings::get('sms_infobip_api_key');
        $baseUrl = SiteSettings::get('sms_infobip_base_url', 'https://api.infobip.com');
        $sender = SiteSettings::get('sms_infobip_sender', 'TONTINE');

        if (!$apiKey) {
            return ['success' => false, 'error' => 'Configuration Infobip incomplète'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "App {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/sms/2/text/advanced", [
                'messages' => [[
                    'from' => $sender,
                    'destinations' => [['to' => '225' . $phone]],
                    'text' => $message,
                ]],
            ]);

            return [
                'success' => $response->successful(),
                'message_id' => $response->json('messages.0.messageId'),
                'error' => $response->successful() ? null : $response->json('requestError.serviceException.text', 'Erreur Infobip'),
            ];
        } catch (\Throwable $e) {
            Log::error('Infobip SMS error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // Orange SMS CI (API locale)
    // ─────────────────────────────────────────────

    private function sendViaOrangeSms(string $phone, string $message): array
    {
        $apiKey = SiteSettings::get('sms_orange_api_key');
        $apiSecret = SiteSettings::get('sms_orange_api_secret');
        $sender = SiteSettings::get('sms_orange_sender_address', 'tel:+2250000');
        $baseUrl = SiteSettings::get('sms_orange_base_url', 'https://api.orange.com/smsmessaging/v1');

        if (!$apiKey || !$apiSecret) {
            return ['success' => false, 'error' => 'Configuration Orange SMS incomplète'];
        }

        try {
            // Token OAuth2
            $tokenResponse = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($apiKey . ':' . $apiSecret),
            ])->asForm()->post('https://api.orange.com/oauth/v3/token', [
                'grant_type' => 'client_credentials',
            ]);

            if (!$tokenResponse->successful()) {
                return ['success' => false, 'error' => 'Échec authentification Orange SMS'];
            }

            $accessToken = $tokenResponse->json('access_token');
            $encodedSender = urlencode($sender);

            $response = Http::withToken($accessToken)
                ->post("{$baseUrl}/outbound/{$encodedSender}/requests", [
                    'outboundSMSMessageRequest' => [
                        'address' => "tel:+225{$phone}",
                        'senderAddress' => $sender,
                        'outboundSMSTextMessage' => ['message' => $message],
                    ],
                ]);

            return [
                'success' => $response->successful(),
                'message_id' => $response->json('outboundSMSMessageRequest.resourceURL'),
                'error' => $response->successful() ? null : 'Erreur Orange SMS',
            ];
        } catch (\Throwable $e) {
            Log::error('Orange SMS error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // Letexto (Fournisseur SMS local Côte d'Ivoire)
    // ─────────────────────────────────────────────

    private function sendViaLetexto(string $phone, string $message): array
    {
        $apiKey = SiteSettings::get('sms_letexto_api_key');
        $sender = SiteSettings::get('sms_letexto_sender', 'TONTINE');
        $baseUrl = SiteSettings::get('sms_letexto_base_url', 'https://api.letexto.com/v1');

        if (!$apiKey) {
            return ['success' => false, 'error' => 'Configuration Letexto incomplète'];
        }

        try {
            $response = Http::withToken($apiKey)
                ->post("{$baseUrl}/campaigns", [
                    'sender' => $sender,
                    'recipient' => ['+225' . $phone],
                    'message' => $message,
                    'type' => 'sms',
                ]);

            return [
                'success' => $response->successful(),
                'message_id' => $response->json('id'),
                'error' => $response->successful() ? null : $response->json('message', 'Erreur Letexto'),
            ];
        } catch (\Throwable $e) {
            Log::error('Letexto SMS error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // Utilitaires
    // ─────────────────────────────────────────────

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '225')) {
            $phone = substr($phone, 3);
        }
        if (str_starts_with($phone, '00225')) {
            $phone = substr($phone, 5);
        }

        return $phone;
    }
}
