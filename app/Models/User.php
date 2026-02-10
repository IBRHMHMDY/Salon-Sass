<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'branch_id',
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

    public function getTenants(Panel $panel): array|Collection
    {
        if ($panel->getId() === 'admin') {
            return $this->salons;
        }

        if ($panel->getId() === 'app') {
            if ($this->hasRoleInsensitive('DevTest')) {
                return Branch::query()->get();
            }

            if ($this->hasRoleInsensitive('Owner')) {
                return Branch::query()
                    ->whereIn('salon_id', $this->salons()->pluck('salons.id'))
                    ->get();
            }

            return $this->branch ? collect([$this->branch]) : collect();
        }

        return [];
    }

    public function canAccessTenant(Model $tenant): bool
    {
        if ($tenant instanceof Salon) {
            return $this->salons->contains($tenant);
        }

        if ($tenant instanceof Branch) {
            if ($this->hasRoleInsensitive('DevTest')) {
                return true;
            }

            if ($this->hasRoleInsensitive('Owner')) {
                return $this->salons()->whereKey($tenant->salon_id)->exists();
            }

            return (int) $this->branch_id === (int) $tenant->getKey();
        }

        return false;
    }

    public function salons(): BelongsToMany
    {
        return $this->belongsToMany(Salon::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRoleInsensitive(['DevTest', 'SuperAdmin']);
        }

        if ($panel->getId() === 'app') {
            return $this->hasAnyRoleInsensitive(['DevTest', 'Owner', 'Manager', 'Employee']);
        }

        return false;
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function hasRoleInsensitive(string $role): bool
    {
        return $this->getRoleNames()->contains(
            fn (string $userRole): bool => strcasecmp($userRole, $role) === 0
        );
    }

    /**
     * @param  array<int, string>  $roles
     */
    public function hasAnyRoleInsensitive(array $roles): bool
    {
        $normalizedRoles = collect($roles)
            ->map(fn (string $role): string => mb_strtolower($role))
            ->values();

        return $this->getRoleNames()->contains(
            fn (string $userRole): bool => $normalizedRoles->contains(mb_strtolower($userRole))
        );
    }
}
