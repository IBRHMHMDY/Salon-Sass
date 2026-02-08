<?php

namespace App\Traits;

use App\Models\Salon;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        // إضافة Global Scope وتفعيله فقط إذا كنا داخل Tenant Context
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Filament::getTenant()) {
                $builder->where('salon_id', Filament::getTenant()->id);
            }
        });

        // تعبئة salon_id تلقائياً عند الإنشاء
        static::creating(function ($model) {
            if (Filament::getTenant()) {
                $model->salon_id = Filament::getTenant()->id;
            }
        });
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
}
