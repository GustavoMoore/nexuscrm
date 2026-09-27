<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LossReason extends Model
{
    protected $fillable = ['name', 'deactivated_at'];

    protected function casts(): array
    {
        return ['deactivated_at' => 'datetime'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
