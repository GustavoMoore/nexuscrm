<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $fillable = ['name'];

    protected function casts(): array
    {
        return ['archived_at' => 'datetime'];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isAdm() ? $query : $query->active()->whereHas('users', fn (Builder $users) => $users->whereKey($user->id));
    }
}
