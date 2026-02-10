<?php

namespace App\Filament\App\Resources\Users\RelationManagers;

use App\Filament\App\Resources\Services\ServiceResource;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Facades\Filament;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    protected static ?string $relatedResource = ServiceResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('اسم الخدمة'),
                TextColumn::make('category.name')->label('القسم'),
                TextColumn::make('price')
                    ->money('SAR')
                    ->label('السعر'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordTitleAttribute('name')
                    ->label('إسناد خدمة')
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query->where('salon_id', Filament::getTenant()?->salon_id ?? 0)
                    ),
            ])
            ->recordActions([
                DetachAction::make()->label('إزالة'),
            ]);
    }
}
