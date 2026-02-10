<?php

namespace App\Traits;

use App\Models\Salon;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            $tenant = Filament::getTenant();
            $salonId = $tenant?->salon_id ?? $tenant?->id;

            if ($salonId) {
                $builder->where('salon_id', $salonId);
            }
        });

        static::creating(function ($model): void {
            $tenant = Filament::getTenant();
            $salonId = $tenant?->salon_id ?? $tenant?->id;

            if ($salonId) {
                $model->salon_id = $salonId;
            }
        });
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
}
