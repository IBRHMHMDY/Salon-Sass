<?php

namespace App\Filament\App\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->label('الاسم'),
                TextColumn::make('roles.name')->badge()->label('الدور'),
                TextColumn::make('branch.name')->label('الفرع'),
                TextColumn::make('created_at')->dateTime()->label('تاريخ الإضافة'),
            ])
            ->filters([
                SelectFilter::make('branch_id')
                    ->relationship('branch', 'name', function (Builder $query): Builder {
                        $tenantSalonId = Filament::getTenant()?->salon_id;

                        return $tenantSalonId
                            ? $query->where('salon_id', $tenantSalonId)
                            : $query;
                    })
                    ->label('تصفية حسب الفرع'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
