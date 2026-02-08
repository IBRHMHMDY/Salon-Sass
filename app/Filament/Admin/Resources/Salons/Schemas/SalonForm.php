<?php

namespace App\Filament\Admin\Resources\Salons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SalonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('logo')
                    ->default(null),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
