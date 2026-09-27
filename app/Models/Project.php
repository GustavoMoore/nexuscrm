<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = ['name'];

    protected static function booted(): void
    {
        static::created(function (Project $project) {
            foreach (['Preço', 'Sem resposta', 'Comprou de outro', 'Sem interesse', 'Outro'] as $name) {
                $project->lossReasons()->create(['name' => $name]);
            }
        });
    }

    protected function casts(): array
    {
        return ['archived_at' => 'datetime'];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function funnels(): HasMany
    {
        return $this->hasMany(Funnel::class);
    }

    public function people(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    public function lossReasons(): HasMany
    {
        return $this->hasMany(LossReason::class);
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
