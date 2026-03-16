<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TontineMessage extends Model
{
    protected $fillable = [
        'tontine_id',
        'user_id',
        'type',
        'content',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForTontine($query, int $tontineId)
    {
        return $query->where('tontine_id', $tontineId);
    }

    public function isSystem(): bool
    {
        return !in_array($this->type, ['text', 'image']);
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->type === 'image' && ($this->metadata['path'] ?? null)) {
            return asset('storage/' . $this->metadata['path']);
        }
        return null;
    }
}
