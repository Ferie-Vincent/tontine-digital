<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'tontine_id',
        'action',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    public function scopeForTontine($query, $tontineId)
    {
        return $query->where('tontine_id', $tontineId);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderByDesc('created_at')->limit($limit);
    }

    public static function log(
        string $action,
        ?Model $subject = null,
        ?int $userId = null,
        ?int $tontineId = null,
        ?array $properties = null
    ): self {
        return self::create([
            'user_id' => $userId ?? auth()->id(),
            'tontine_id' => $tontineId,
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'a créé',
            'updated' => 'a modifié',
            'deleted' => 'a supprimé',
            'joined' => 'a rejoint',
            'left' => 'a quitté',
            'contributed' => 'a contribué',
            'contributed_for_member' => 'a déclaré pour un membre',
            'updated_parts' => 'a modifié les parts de',
            'confirmed' => 'a confirmé',
            'rejected' => 'a rejeté',
            'started_tour' => 'a démarré le tour',
            'completed_tour' => 'a terminé le tour',
            'auto_completed_tour' => 'a cloturé automatiquement le tour',
            'sent_reminder' => 'a envoyé un rappel',
            'marked_late' => 'a marqué en retard',
            'auto_excluded' => 'a exclu automatiquement',
            'tour_failed' => 'a marqué le tour en échec',
            'tour_retried' => 'a relancé le tour',
            'updated_settings' => 'a modifié les paramètres',
            'member_left' => 'a quitté la tontine',
            'auto_confirmed_receipt' => 'a auto-confirmé la réception',
            'cloned' => 'a dupliqué',
            'swap_requested' => 'a demandé un échange de position',
            'position_swapped' => 'a échangé de position',
            'impersonate_start' => 'a pris l\'identité de',
            'impersonate_stop' => 'a quitté le mode impersonation',
            default => $this->action,
        };
    }
}
