<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Branch extends Model
{
    protected $guarded = [];

    public function salon(): BelongsTo
    {
        return $this->belongsTo(Salon::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function managers(): HasMany
    {
        return $this->hasMany(User::class, 'branch_id')
            ->whereHas('roles', fn (Builder $query) => $query->whereRaw('LOWER(name) = ?', ['manager']));
    }
}
