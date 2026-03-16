<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\SiteSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    /**
     * Vérifie les paiements reçus auprès de tous les opérateurs actifs
     * et tente de faire correspondre avec les contributions en attente.
     */
    public function checkIncomingPayments(array $pendingContributions): array
    {
        $results = ['confirmed' => 0, 'failed' => 0, 'errors' => []];

        $providers = $this->getActiveProviders();

        foreach ($providers as $provider) {
            try {
                $transactions = $this->fetchTransactions($provider);

                foreach ($transactions as $transaction) {
                    $matched = $this->matchTransaction($transaction, $pendingContributions, $provider);
                    if ($matched) {
                        $results['confirmed']++;
                    }
                }
            } catch (\Throwable $e) {
                $results['errors'][] = "{$provider}: {$e->getMessage()}";
                Log::error("PaymentGateway [{$provider}] error: {$e->getMessage()}");
            }
        }

        return $results;
    }

    /**
     * Retourne la liste des opérateurs activés avec leurs clés configurées.
     */
    public function getActiveProviders(): array
    {
        $providers = [];

        if (SiteSettings::getBoolean('pg_orange_enabled', false) && SiteSettings::get('pg_orange_api_key')) {
            $providers[] = 'orange_money';
        }
        if (SiteSettings::getBoolean('pg_mtn_enabled', false) && SiteSettings::get('pg_mtn_api_key')) {
            $providers[] = 'mtn_momo';
        }
        if (SiteSettings::getBoolean('pg_moov_enabled', false) && SiteSettings::get('pg_moov_api_key')) {
            $providers[] = 'moov_money';
        }
        if (SiteSettings::getBoolean('pg_wave_enabled', false) && SiteSettings::get('pg_wave_api_key')) {
            $providers[] = 'wave';
        }

        return $providers;
    }

    /**
     * Récupère les transactions récentes d'un opérateur via son API.
     */
    public function fetchTransactions(string $provider): array
    {
        return match ($provider) {
            'orange_money' => $this->fetchOrangeMoneyTransactions(),
            'mtn_momo' => $this->fetchMtnMomoTransactions(),
            'moov_money' => $this->fetchMoovMoneyTransactions(),
            'wave' => $this->fetchWaveTransactions(),
            default => [],
        };
    }

    /**
     * Envoie un paiement vers un bénéficiaire (pour le décaissement automatique).
     */
    public function sendPayment(string $provider, string $phoneNumber, int $amount, string $reference): array
    {
        return match ($provider) {
            'orange_money' => $this->sendOrangeMoneyPayment($phoneNumber, $amount, $reference),
            'mtn_momo' => $this->sendMtnMomoPayment($phoneNumber, $amount, $reference),
            'moov_money' => $this->sendMoovMoneyPayment($phoneNumber, $amount, $reference),
            'wave' => $this->sendWavePayment($phoneNumber, $amount, $reference),
            default => ['success' => false, 'error' => 'Opérateur non supporté'],
        };
    }

    /**
     * Tente de faire correspondre une transaction reçue avec une contribution en attente.
     */
    private function matchTransaction(array $transaction, array $pendingContributions, string $provider): bool
    {
        foreach ($pendingContributions as $contribution) {
            $user = $contribution->user;
            if (!$user) continue;

            $phoneMatch = $this->normalizePhone($transaction['sender_phone'] ?? '') === $this->normalizePhone($user->phone ?? '');
            $amountMatch = (int) ($transaction['amount'] ?? 0) === (int) $contribution->amount;

            if ($phoneMatch && $amountMatch) {
                $contribution->update([
                    'status' => 'confirmed',
                    'confirmed_at' => now(),
                    'notes' => "Auto-confirmé via {$provider} - Réf: " . ($transaction['reference'] ?? 'N/A'),
                ]);

                // Mettre à jour la preuve de paiement si elle existe
                if ($contribution->paymentProof) {
                    $contribution->paymentProof->update([
                        'verification_status' => 'verified',
                        'verified_at' => now(),
                        'transaction_reference' => $transaction['reference'] ?? null,
                    ]);
                }

                return true;
            }
        }

        return false;
    }

    // ─────────────────────────────────────────────
    // Orange Money CI - API
    // ─────────────────────────────────────────────

    private function fetchOrangeMoneyTransactions(): array
    {
        $apiKey = SiteSettings::get('pg_orange_api_key');
        $merchantId = SiteSettings::get('pg_orange_merchant_id');
        $baseUrl = SiteSettings::get('pg_orange_base_url', 'https://api.orange.com/orange-money-webpay/dev/v1');

        try {
            // Authentification OAuth2
            $tokenResponse = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($apiKey . ':' . SiteSettings::get('pg_orange_api_secret', '')),
            ])->post("{$baseUrl}/token", [
                'grant_type' => 'client_credentials',
            ]);

            if (!$tokenResponse->successful()) {
                Log::warning('Orange Money: Échec authentification', ['status' => $tokenResponse->status()]);
                return [];
            }

            $accessToken = $tokenResponse->json('access_token');

            // Récupérer les transactions des dernières 4 heures
            $response = Http::withToken($accessToken)
                ->get("{$baseUrl}/merchants/{$merchantId}/transactions", [
                    'start_date' => now()->subHours(4)->toIso8601String(),
                    'end_date' => now()->toIso8601String(),
                    'status' => 'SUCCESS',
                ]);

            if (!$response->successful()) {
                Log::warning('Orange Money: Échec récupération transactions', ['status' => $response->status()]);
                return [];
            }

            return collect($response->json('transactions', []))->map(fn($t) => [
                'reference' => $t['txn_id'] ?? $t['transaction_id'] ?? null,
                'sender_phone' => $t['customer_msisdn'] ?? $t['sender'] ?? null,
                'amount' => $t['amount'] ?? 0,
                'date' => $t['created_at'] ?? $t['date'] ?? null,
                'provider' => 'orange_money',
            ])->toArray();
        } catch (\Throwable $e) {
            Log::error('Orange Money API error: ' . $e->getMessage());
            return [];
        }
    }

    private function sendOrangeMoneyPayment(string $phone, int $amount, string $reference): array
    {
        $apiKey = SiteSettings::get('pg_orange_api_key');
        $baseUrl = SiteSettings::get('pg_orange_base_url', 'https://api.orange.com/orange-money-webpay/dev/v1');

        try {
            $tokenResponse = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($apiKey . ':' . SiteSettings::get('pg_orange_api_secret', '')),
            ])->post("{$baseUrl}/token", ['grant_type' => 'client_credentials']);

            if (!$tokenResponse->successful()) {
                return ['success' => false, 'error' => 'Échec authentification Orange Money'];
            }

            $response = Http::withToken($tokenResponse->json('access_token'))
                ->post("{$baseUrl}/cashout", [
                    'receiver_msisdn' => $phone,
                    'amount' => $amount,
                    'currency' => 'XOF',
                    'reference' => $reference,
                ]);

            return [
                'success' => $response->successful(),
                'reference' => $response->json('txn_id'),
                'error' => $response->successful() ? null : $response->json('message', 'Erreur Orange Money'),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // MTN MoMo CI - API
    // ─────────────────────────────────────────────

    private function fetchMtnMomoTransactions(): array
    {
        $apiKey = SiteSettings::get('pg_mtn_api_key');
        $subscriptionKey = SiteSettings::get('pg_mtn_subscription_key');
        $baseUrl = SiteSettings::get('pg_mtn_base_url', 'https://sandbox.momodeveloper.mtn.com/collection/v1_0');
        $targetEnv = SiteSettings::get('pg_mtn_environment', 'sandbox');

        try {
            // Authentification avec API User + API Key
            $tokenResponse = Http::withBasicAuth(
                SiteSettings::get('pg_mtn_api_user', ''),
                $apiKey
            )->withHeaders([
                'Ocp-Apim-Subscription-Key' => $subscriptionKey,
            ])->post("{$baseUrl}/token/");

            if (!$tokenResponse->successful()) {
                Log::warning('MTN MoMo: Échec authentification', ['status' => $tokenResponse->status()]);
                return [];
            }

            $accessToken = $tokenResponse->json('access_token');

            // Récupérer l'historique des transactions (collection)
            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'X-Target-Environment' => $targetEnv,
                    'Ocp-Apim-Subscription-Key' => $subscriptionKey,
                ])
                ->get("{$baseUrl}/account/transactions", [
                    'from' => now()->subHours(4)->toIso8601String(),
                ]);

            if (!$response->successful()) {
                Log::warning('MTN MoMo: Échec récupération transactions', ['status' => $response->status()]);
                return [];
            }

            return collect($response->json('transactions', []))->map(fn($t) => [
                'reference' => $t['financialTransactionId'] ?? $t['referenceId'] ?? null,
                'sender_phone' => $t['payer']['partyId'] ?? null,
                'amount' => $t['amount'] ?? 0,
                'date' => $t['created'] ?? null,
                'provider' => 'mtn_momo',
            ])->toArray();
        } catch (\Throwable $e) {
            Log::error('MTN MoMo API error: ' . $e->getMessage());
            return [];
        }
    }

    private function sendMtnMomoPayment(string $phone, int $amount, string $reference): array
    {
        $apiKey = SiteSettings::get('pg_mtn_api_key');
        $subscriptionKey = SiteSettings::get('pg_mtn_subscription_key');
        $baseUrl = str_replace('/collection/', '/disbursement/', SiteSettings::get('pg_mtn_base_url', 'https://sandbox.momodeveloper.mtn.com/disbursement/v1_0'));
        $targetEnv = SiteSettings::get('pg_mtn_environment', 'sandbox');

        try {
            $tokenResponse = Http::withBasicAuth(
                SiteSettings::get('pg_mtn_api_user', ''),
                $apiKey
            )->withHeaders([
                'Ocp-Apim-Subscription-Key' => $subscriptionKey,
            ])->post("{$baseUrl}/token/");

            if (!$tokenResponse->successful()) {
                return ['success' => false, 'error' => 'Échec authentification MTN MoMo'];
            }

            $response = Http::withToken($tokenResponse->json('access_token'))
                ->withHeaders([
                    'X-Target-Environment' => $targetEnv,
                    'X-Reference-Id' => $reference,
                    'Ocp-Apim-Subscription-Key' => $subscriptionKey,
                ])
                ->post("{$baseUrl}/v1_0/transfer", [
                    'amount' => (string) $amount,
                    'currency' => 'XOF',
                    'externalId' => $reference,
                    'payee' => ['partyIdType' => 'MSISDN', 'partyId' => $phone],
                    'payerMessage' => 'Décaissement tontine',
                    'payeeNote' => 'Décaissement automatique',
                ]);

            return [
                'success' => $response->successful() || $response->status() === 202,
                'reference' => $reference,
                'error' => $response->successful() ? null : $response->json('message', 'Erreur MTN MoMo'),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // Moov Money CI - API
    // ─────────────────────────────────────────────

    private function fetchMoovMoneyTransactions(): array
    {
        $apiKey = SiteSettings::get('pg_moov_api_key');
        $merchantCode = SiteSettings::get('pg_moov_merchant_code');
        $baseUrl = SiteSettings::get('pg_moov_base_url', 'https://api.moov-africa.com/payment/v1');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'X-Merchant-Code' => $merchantCode,
            ])->get("{$baseUrl}/transactions", [
                'start_date' => now()->subHours(4)->format('Y-m-d H:i:s'),
                'end_date' => now()->format('Y-m-d H:i:s'),
                'status' => 'SUCCESS',
                'type' => 'COLLECTION',
            ]);

            if (!$response->successful()) {
                Log::warning('Moov Money: Échec récupération transactions', ['status' => $response->status()]);
                return [];
            }

            return collect($response->json('data', []))->map(fn($t) => [
                'reference' => $t['transaction_id'] ?? null,
                'sender_phone' => $t['customer_phone'] ?? $t['sender_msisdn'] ?? null,
                'amount' => $t['amount'] ?? 0,
                'date' => $t['created_at'] ?? null,
                'provider' => 'moov_money',
            ])->toArray();
        } catch (\Throwable $e) {
            Log::error('Moov Money API error: ' . $e->getMessage());
            return [];
        }
    }

    private function sendMoovMoneyPayment(string $phone, int $amount, string $reference): array
    {
        $apiKey = SiteSettings::get('pg_moov_api_key');
        $merchantCode = SiteSettings::get('pg_moov_merchant_code');
        $baseUrl = SiteSettings::get('pg_moov_base_url', 'https://api.moov-africa.com/payment/v1');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'X-Merchant-Code' => $merchantCode,
            ])->post("{$baseUrl}/cashout", [
                'receiver_phone' => $phone,
                'amount' => $amount,
                'currency' => 'XOF',
                'reference' => $reference,
                'description' => 'Décaissement tontine',
            ]);

            return [
                'success' => $response->successful(),
                'reference' => $response->json('transaction_id'),
                'error' => $response->successful() ? null : $response->json('message', 'Erreur Moov Money'),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // Wave CI - API
    // ─────────────────────────────────────────────

    private function fetchWaveTransactions(): array
    {
        $apiKey = SiteSettings::get('pg_wave_api_key');
        $baseUrl = SiteSettings::get('pg_wave_base_url', 'https://api.wave.com/v1');

        try {
            $response = Http::withToken($apiKey)
                ->get("{$baseUrl}/transactions", [
                    'after' => now()->subHours(4)->toIso8601String(),
                    'type' => 'PAYMENT',
                    'status' => 'SUCCEEDED',
                ]);

            if (!$response->successful()) {
                Log::warning('Wave: Échec récupération transactions', ['status' => $response->status()]);
                return [];
            }

            return collect($response->json('items', []))->map(fn($t) => [
                'reference' => $t['id'] ?? $t['transaction_id'] ?? null,
                'sender_phone' => $t['sender_mobile'] ?? $t['client_phone'] ?? null,
                'amount' => $t['amount'] ?? $t['receive_amount'] ?? 0,
                'date' => $t['timestamp'] ?? $t['created_at'] ?? null,
                'provider' => 'wave',
            ])->toArray();
        } catch (\Throwable $e) {
            Log::error('Wave API error: ' . $e->getMessage());
            return [];
        }
    }

    private function sendWavePayment(string $phone, int $amount, string $reference): array
    {
        $apiKey = SiteSettings::get('pg_wave_api_key');
        $baseUrl = SiteSettings::get('pg_wave_base_url', 'https://api.wave.com/v1');

        try {
            $response = Http::withToken($apiKey)
                ->post("{$baseUrl}/payout", [
                    'receive_amount' => $amount,
                    'currency' => 'XOF',
                    'mobile' => $phone,
                    'client_reference' => $reference,
                    'name' => 'Décaissement tontine',
                ]);

            return [
                'success' => $response->successful(),
                'reference' => $response->json('id') ?? $response->json('transaction_id'),
                'error' => $response->successful() ? null : $response->json('message', 'Erreur Wave'),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // Utilitaires
    // ─────────────────────────────────────────────

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Normaliser les numéros ivoiriens (+225)
        if (str_starts_with($phone, '225')) {
            $phone = substr($phone, 3);
        }
        if (str_starts_with($phone, '00225')) {
            $phone = substr($phone, 5);
        }

        return $phone;
    }
}
