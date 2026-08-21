<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Minute extends Model
{
    protected $fillable = [
        'group_id',
        'session_topic',
        'notulis_name',
        'session_date',
        'problem',
        'cause',
        'solution',
        'action_ppg',
        'action_description',
        'action_name',
        'action_participants',
        'action_time',
        'action_budget',
        'role_keimaman',
        'role_pengurus',
        'role_parents',
        'role_muballigh',
        'role_educator',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
