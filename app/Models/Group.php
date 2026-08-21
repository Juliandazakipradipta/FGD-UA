<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function minutes(): HasMany
    {
        return $this->hasMany(Minute::class);
    }
}
