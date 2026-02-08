<?php

namespace App\Filament\Admin\Resources\Salons;

use App\Filament\Admin\Resources\Salons\Pages\CreateSalon;
use App\Filament\Admin\Resources\Salons\Pages\EditSalon;
use App\Filament\Admin\Resources\Salons\Pages\ListSalons;
use App\Filament\Admin\Resources\Salons\Schemas\SalonForm;
use App\Filament\Admin\Resources\Salons\Tables\SalonsTable;
use App\Models\Salon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SalonResource extends Resource
{
    protected static ?string $model = Salon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Salon';

    public static function form(Schema $schema): Schema
    {
        return SalonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalonsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalons::route('/'),
            'create' => CreateSalon::route('/create'),
            'edit' => EditSalon::route('/{record}/edit'),
        ];
    }
}
