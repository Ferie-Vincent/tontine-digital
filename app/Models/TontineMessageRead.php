<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TontineMessageRead extends Model
{
    protected $fillable = [
        'user_id',
        'tontine_id',
        'last_read_at',
    ];

    protected function casts(): array
    {
        return [
            'last_read_at' => 'datetime',
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
}
