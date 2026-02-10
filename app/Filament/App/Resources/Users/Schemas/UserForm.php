<?php

namespace App\Filament\App\Resources\Users\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('البريد الإلكتروني'),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->visibleOn('create') // يمكن إخفاؤه في التعديل أو جعله اختيارياً
                    ->label('كلمة المرور'),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label('الدور الوظيفي')
                    // عرض الأدوار المسموح بها فقط داخل الصالون
                    ->options(function () {
                        return \Spatie\Permission\Models\Role::whereRaw('LOWER(name) IN (?, ?)', ['manager', 'employee'])
                            ->pluck('name', 'id');
                    }),
                Select::make('branch_id')
                    ->relationship(
                        'branch',
                        'name',
                        fn (Builder $query) => $query->where('salon_id', Filament::getTenant()?->salon_id ?? 0)
                    )
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label('الفرع التابع له'),
            ]);
    }
}

