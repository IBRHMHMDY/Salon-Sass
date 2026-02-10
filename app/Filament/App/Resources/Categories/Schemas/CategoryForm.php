<?php

namespace App\Filament\App\Resources\Categories\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('salon_id')
                    ->relationship(
                        'salon',
                        'name',
                        fn (Builder $query) => $query->whereKey(Filament::getTenant()?->salon_id ?? 0)
                    )
                    ->default(fn () => Filament::getTenant()?->salon_id)
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                TextInput::make('name')
                    ->required(),
                FileUpload::make('image')
                    ->image(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
