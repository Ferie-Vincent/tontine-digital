<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class TestMessagingController extends Controller
{
    public function testSms(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string',
        ]);

        $smsService = app(SmsService::class);

        if (!$smsService->isEnabled()) {
            return back()->with('error', 'Le service SMS est désactivé. Activez un fournisseur d\'abord.');
        }

        $result = $smsService->send(
            $request->test_phone,
            'Ceci est un message de test de la plateforme Digi-Tontine. Si vous recevez ce message, la configuration SMS fonctionne correctement.'
        );

        if ($result['success']) {
            return back()->with('success', 'SMS de test envoyé avec succès ! Vérifiez le téléphone ' . $request->test_phone);
        }

        return back()->with('error', 'Échec de l\'envoi SMS : ' . ($result['error'] ?? 'Erreur inconnue'));
    }

    public function testWhatsapp(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string',
        ]);

        $whatsappService = app(WhatsAppService::class);

        if (!$whatsappService->isEnabled()) {
            return back()->with('error', 'Le service WhatsApp est désactivé. Activez un fournisseur d\'abord.');
        }

        $result = $whatsappService->send(
            $request->test_phone,
            'Ceci est un message de test de la plateforme Digi-Tontine. Si vous recevez ce message, la configuration WhatsApp fonctionne correctement.'
        );

        if ($result['success']) {
            return back()->with('success', 'Message WhatsApp de test envoyé avec succès !');
        }

        return back()->with('error', 'Échec de l\'envoi WhatsApp : ' . ($result['error'] ?? 'Erreur inconnue'));
    }
}
