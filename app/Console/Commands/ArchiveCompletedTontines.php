<?php

namespace App\Console\Commands;

use App\Enums\TontineStatus;
use App\Models\ActivityLog;
use App\Models\Tontine;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class ArchiveCompletedTontines extends Command
{
    protected $signature = 'tontine:archive-completed';

    protected $description = 'Archive les tontines terminées depuis un certain nombre de jours';

    public function handle(NotificationService $notificationService): int
    {
        $archiveAfterDays = (int) \App\Models\SiteSettings::get('archive_after_days', 30);
        $totalArchived = 0;

        $tontines = Tontine::where('status', TontineStatus::COMPLETED)
            ->where('updated_at', '<=', now()->subDays($archiveAfterDays))
            ->get();

        foreach ($tontines as $tontine) {
            $tontine->delete(); // SoftDelete — la tontine reste en base mais n'apparaît plus

            $totalArchived++;

            ActivityLog::log('auto_archived', $tontine, tontineId: $tontine->id, properties: [
                'days_since_completion' => (int) $tontine->updated_at->diffInDays(now()),
                'archive_threshold' => $archiveAfterDays,
            ]);

            $notificationService->notifyTontineManagers(
                $tontine,
                'tontine_archived',
                'Tontine archivée',
                "La tontine {$tontine->name} a été archivée automatiquement après {$archiveAfterDays} jours d'inactivité. Elle reste consultable dans les archives.",
                ['tontine_id' => $tontine->id],
                sendEmail: true
            );
        }

        $this->info("Tontines archivées : {$totalArchived}.");

        return self::SUCCESS;
    }
}
