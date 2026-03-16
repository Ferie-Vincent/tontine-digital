<?php

namespace App\Models;

use App\Enums\TontineFrequency;
use App\Enums\TontineStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tontine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'creator_id',
        'name',
        'description',
        'code',
        'contribution_amount',
        'target_amount_per_tour',
        'target_amount_total',
        'frequency',
        'max_members',
        'start_date',
        'end_date',
        'status',
        'rules',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'contribution_amount' => 'integer',
            'target_amount_per_tour' => 'integer',
            'target_amount_total' => 'integer',
            'max_members' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => TontineStatus::class,
            'frequency' => TontineFrequency::class,
            'settings' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Tontine $tontine) {
            if (empty($tontine->code)) {
                $tontine->code = self::generateUniqueCode();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->hasMany(TontineMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'tontine_members')
            ->withPivot('role', 'position', 'parts', 'status', 'joined_at')
            ->withTimestamps();
    }

    public function activeMembers()
    {
        return $this->members()->where('status', 'active');
    }

    public function messages()
    {
        return $this->hasMany(TontineMessage::class)->orderBy('created_at');
    }

    public function tours()
    {
        return $this->hasMany(Tour::class)->orderBy('tour_number');
    }

    public function contributions()
    {
        return $this->hasMany(Contribution::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', TontineStatus::ACTIVE);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->whereHas('members', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->whereIn('status', ['active', 'pending']);
        });
    }

    public function getFormattedAmountAttribute(): string
    {
        return format_amount($this->contribution_amount);
    }

    public function getFormattedTargetPerTourAttribute(): ?string
    {
        if (!$this->target_amount_per_tour) return null;
        return format_amount($this->target_amount_per_tour);
    }

    public function getFormattedTargetTotalAttribute(): ?string
    {
        if (!$this->target_amount_total) return null;
        return format_amount($this->target_amount_total);
    }

    public function getActiveMembersCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }

    public function getCurrentTourAttribute(): ?Tour
    {
        return $this->tours()->where('status', 'ongoing')->first();
    }

    public function getNextTourAttribute(): ?Tour
    {
        return $this->tours()->where('status', 'upcoming')->orderBy('due_date')->first();
    }

    public function getProgressAttribute(): float
    {
        // Si withCount a été utilisé en amont, utiliser les valeurs cachées
        if (isset($this->attributes['tours_count']) && isset($this->attributes['completed_tours_count'])) {
            $total = $this->attributes['tours_count'];
            $completed = $this->attributes['completed_tours_count'];
        } else {
            // Une seule requête au lieu de deux
            $counts = $this->tours()->selectRaw('count(*) as total, sum(case when status = ? then 1 else 0 end) as completed', ['completed'])->first();
            $total = $counts->total;
            $completed = $counts->completed;
        }

        if ($total === 0) return 0;
        return round(($completed / $total) * 100, 1);
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());
        return $code;
    }

    public function getTotalPartsAttribute(): int
    {
        return $this->activeMembers()->sum('parts') ?: 0;
    }

    public function getPotAmountAttribute(): int
    {
        return $this->total_parts * $this->contribution_amount;
    }

    public function getFormattedPotAmountAttribute(): string
    {
        return format_amount($this->pot_amount);
    }

    public function isFull(): bool
    {
        return $this->activeMembers()->count() >= $this->max_members;
    }

    public function canJoin(): bool
    {
        return $this->status === TontineStatus::ACTIVE && !$this->isFull();
    }

    // --- Tontine Settings Helpers ---

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings ?? self::defaultSettings(), $key, $default);
    }

    public function setSetting(string $key, mixed $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['settings' => $settings]);
    }

    public static function defaultSettings(): array
    {
        return [
            'late_detection_enabled' => false,
            'late_threshold_days' => 3,
            'late_penalty_amount' => 0,
            'auto_exclusion_enabled' => false,
            'auto_exclusion_threshold' => 3,
            'reminder_days_before' => [3, 1, 0],
            'tour_failure_enabled' => false,
            'tour_failure_grace_days' => 7,
            'tour_failure_min_collection_percent' => 50,
            // Sprint 1 — Automatisations du cycle de vie
            'auto_generate_tours' => false,
            'auto_start_tours' => false,
            'auto_status_transitions' => false,
            'min_members_to_start' => 3,
            // Sprint 2 — Automatisations avancées
            'auto_disburse_reminder' => false,
            'disburse_reminder_delay_hours' => 24,
            'collection_alerts_enabled' => false,
            'collection_alert_thresholds' => [
                ['days_before' => 5, 'min_percent' => 30],
                ['days_before' => 3, 'min_percent' => 50],
                ['days_before' => 1, 'min_percent' => 80],
            ],
            'auto_reports_enabled' => false,
            'report_frequency' => 'weekly',
            'report_send_to_members' => false,
            // Pénalités configurables
            'penalty_enabled' => false,
            'penalty_type' => 'fixed',
            'penalty_amount' => 0,
            'penalty_grace_hours' => 24,
            // Sprint 3 — Optimisations
            'auto_reinstate_enabled' => false,
            'reinstate_grace_days' => 7,
            'auto_refund_penalty' => false,
            // Sprint 5 — Expérience utilisateur
            'auto_close_tour_enabled' => false,
            'auto_close_tour_days' => 7,
        ];
    }
}
