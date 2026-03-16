<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    protected $signature = 'webpush:vapid';
    protected $description = 'Générer les clés VAPID pour les notifications push';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();

        $this->info('Clés VAPID générées avec succès !');
        $this->newLine();
        $this->line('Ajoutez ces lignes dans votre fichier .env :');
        $this->newLine();
        $this->line("VAPID_PUBLIC_KEY={$keys['publicKey']}");
        $this->line("VAPID_PRIVATE_KEY={$keys['privateKey']}");
        $this->line("VITE_VAPID_PUBLIC_KEY={$keys['publicKey']}");

        return Command::SUCCESS;
    }
}
