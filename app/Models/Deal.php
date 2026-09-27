<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deal extends Model
{
    protected $fillable = ['stage_id', 'person_id', 'value', 'next_step', 'next_step_date', 'status', 'loss_reason_id', 'closed_at'];

    protected function casts(): array
    {
        return ['next_step_date' => 'date:Y-m-d', 'closed_at' => 'datetime', 'value' => 'decimal:2'];
    }

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function lossReason(): BelongsTo
    {
        return $this->belongsTo(LossReason::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(DealNote::class)->orderByDesc('created_at')->orderByDesc('id');
    }
}
