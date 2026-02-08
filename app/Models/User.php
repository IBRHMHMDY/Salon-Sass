<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 1. تحديد الصالونات التي ينتمي لها المستخدم
    public function getTenants(Panel $panel): Collection
    {
        return $this->salons;
    }

    // 2. التحقق من إمكانية الوصول للصالون
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->salons->contains($tenant);
    }

    // 3. تطبيق Tenancy
    public function salons(): BelongsToMany
    {
        return $this->belongsToMany(Salon::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // سنقوم بتخصيص هذا لاحقاً بناءً على الأدوار (DevTest, SuperAdmin, Owner...)
        return true;
    }
}
