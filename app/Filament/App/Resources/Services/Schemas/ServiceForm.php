<?php

namespace App\Filament\App\Resources\Services\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('salon_id')
                    ->default(fn () => Filament::getTenant()?->salon_id)
                    ->dehydrated()
                    ->required(),

                Select::make('category_id')
                    ->relationship('category', 'name', function (Builder $query): Builder {
                        $tenantSalonId = Filament::getTenant()?->salon_id;

                        return $tenantSalonId
                            ? $query->where('salon_id', $tenantSalonId)
                            : $query->whereRaw('1 = 0');
                    })
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                        Hidden::make('salon_id')->default(fn () => Filament::getTenant()?->salon_id),
                    ])
                    ->label('القسم'),

                TextInput::make('name')
                    ->required()
                    ->label('اسم الخدمة'),

                Textarea::make('description')
                    ->label('وصف الخدمة')
                    ->columnSpanFull(),

                Grid::make(2)
                    ->schema([
                        TextInput::make('price')
                            ->numeric()
                            ->prefix('SAR')
                            ->required()
                            ->label('السعر'),

                        TextInput::make('duration_minutes')
                            ->numeric()
                            ->step(5)
                            ->suffix('دقيقة')
                            ->required()
                            ->label('مدة الخدمة'),
                    ]),

                Toggle::make('is_active')
                    ->default(true)
                    ->label('متاحة للحجز'),
            ]);
    }
}
