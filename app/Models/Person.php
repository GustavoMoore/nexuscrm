<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    protected $fillable = ['name', 'phone', 'email'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }
}
