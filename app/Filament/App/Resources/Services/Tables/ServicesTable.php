<?php

namespace App\Filament\App\Resources\Services\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->label('الخدمة'),
                TextColumn::make('category.name')->sortable()->label('القسم'),
                TextColumn::make('price')->money('SAR')->label('السعر'),
                TextColumn::make('duration_minutes')->suffix(' دقيقة')->label('المدة'),
                ToggleColumn::make('is_active')->label('نشط'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name', function (Builder $query): Builder {
                        $tenantSalonId = Filament::getTenant()?->salon_id;

                        return $tenantSalonId
                            ? $query->where('salon_id', $tenantSalonId)
                            : $query->whereRaw('1 = 0');
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
