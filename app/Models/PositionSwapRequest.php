<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionSwapRequest extends Model
{
    protected $fillable = [
        'tontine_id',
        'requester_id',
        'target_id',
        'requester_position',
        'target_position',
        'reason',
        'status',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function target()
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}
