<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stage extends Model
{
    protected $fillable = ['name', 'position'];

    public function funnel(): BelongsTo
    {
        return $this->belongsTo(Funnel::class);
    }
}
