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
    // إعداد الـ Multi-tenancy (سنفترض أن Tenant هو موديل Salon)
    public function getTenants(Panel $panel): array|Collection
    {
        return $this->salons; // العلاقة مع الصالونات
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
        if ($panel->getId() === 'admin') {
            return $this->hasRole(['DevTest', 'SuperAdmin']);
        }

        if ($panel->getId() === 'app') {
            // Owners, Managers, Employees يدخلون لوحة التطبيق
            return $this->hasRole(['DevTest', 'Owner', 'Manager', 'Employee']);
        }

        return false;
    }
}
