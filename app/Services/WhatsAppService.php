<?php

namespace App\Services;

use App\Models\SiteSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envoie un message WhatsApp à un numéro de téléphone.
     */
    public function send(string $phone, string $message): array
    {
        $provider = $this->getActiveProvider();

        if ($provider === 'disabled') {
            Log::info("[WhatsApp] Service désactivé. Message pour {$phone}: {$message}");
            return ['success' => false, 'error' => 'WhatsApp désactivé'];
        }

        $phone = $this->normalizePhone($phone);

        try {
            return match ($provider) {
                'twilio' => $this->sendViaTwilio($phone, $message),
                'meta' => $this->sendViaMeta($phone, $message),
                default => ['success' => false, 'error' => "Fournisseur WhatsApp inconnu : {$provider}"],
            };
        } catch (\Exception $e) {
            Log::error("[WhatsApp] Erreur d'envoi via {$provider}: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Envoie un message WhatsApp à plusieurs numéros.
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
     * Vérifie si le service WhatsApp est activé et configuré.
     */
    public function isEnabled(): bool
    {
        return $this->getActiveProvider() !== 'disabled';
    }

    /**
     * Retourne le nom du fournisseur actif.
     */
    public function getActiveProvider(): string
    {
        return SiteSettings::get('whatsapp_provider', 'disabled');
    }

    // ─────────────────────────────────────────────
    // Twilio WhatsApp
    // ─────────────────────────────────────────────

    private function sendViaTwilio(string $phone, string $message): array
    {
        $sid = SiteSettings::get('whatsapp_twilio_sid');
        $token = SiteSettings::get('whatsapp_twilio_token');
        $from = SiteSettings::get('whatsapp_twilio_from');

        if (!$sid || !$token || !$from) {
            return ['success' => false, 'error' => 'Configuration Twilio WhatsApp incomplète'];
        }

        try {
            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => "whatsapp:{$from}",
                    'To' => "whatsapp:+225{$phone}",
                    'Body' => $message,
                ]);

            return [
                'success' => $response->successful(),
                'message_id' => $response->json('sid'),
                'error' => $response->successful() ? null : $response->json('message', 'Erreur Twilio WhatsApp'),
            ];
        } catch (\Throwable $e) {
            Log::error('Twilio WhatsApp error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // Meta (WhatsApp Cloud API)
    // ─────────────────────────────────────────────

    private function sendViaMeta(string $phone, string $message): array
    {
        $accessToken = SiteSettings::get('whatsapp_meta_access_token');
        $phoneNumberId = SiteSettings::get('whatsapp_meta_phone_number_id');
        $apiVersion = SiteSettings::get('whatsapp_meta_api_version', 'v18.0');

        if (!$accessToken || !$phoneNumberId) {
            return ['success' => false, 'error' => 'Configuration Meta WhatsApp incomplète'];
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => "225{$phone}",
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]);

            return [
                'success' => $response->successful(),
                'message_id' => $response->json('messages.0.id'),
                'error' => $response->successful() ? null : $response->json('error.message', 'Erreur Meta WhatsApp'),
            ];
        } catch (\Throwable $e) {
            Log::error('Meta WhatsApp error: ' . $e->getMessage());
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
