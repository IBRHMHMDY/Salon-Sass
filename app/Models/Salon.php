<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salon extends Model
{
    protected $fillable = ['name', 'slug', 'logo', 'is_active'];

    // العلاقة مع المستخدمين (Members)
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
