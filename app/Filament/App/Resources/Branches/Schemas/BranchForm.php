<?php

namespace App\Filament\App\Resources\Branches\Schemas;

use App\Models\Branch;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class BranchForm
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

                TextInput::make('address')
                    ->default(null),

                TextInput::make('phone')
                    ->tel()
                    ->default(null),

                Select::make('manager_id')
                    ->label('Manager')
                    ->options(function (): array {
                        $editedBranch = static::resolveEditedBranch();
                        $tenantSalonId = $editedBranch?->salon_id ?? Filament::getTenant()?->salon_id;
                        $selectedManagerId = $editedBranch?->manager_id;

                        $options = User::query()
                            ->whereHas('roles', fn (Builder $roleQuery) => $roleQuery->whereRaw('LOWER(name) = ?', ['manager']))
                            ->where(function (Builder $managerQuery) use ($tenantSalonId): Builder {
                                if (! $tenantSalonId) {
                                    return $managerQuery->whereRaw('1 = 0');
                                }

                                return $managerQuery
                                    ->whereNull('branch_id')
                                    ->orWhereHas('branch', fn (Builder $branchQuery) => $branchQuery->where('salon_id', $tenantSalonId));
                            })
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all();

                        if ($selectedManagerId && ! array_key_exists($selectedManagerId, $options)) {
                            $selectedManagerName = User::query()
                                ->whereKey($selectedManagerId)
                                ->value('name');

                            if ($selectedManagerName) {
                                $options[(string) $selectedManagerId] = $selectedManagerName;
                            }
                        }

                        return collect($options)
                            ->mapWithKeys(fn (string $name, int|string $id): array => [(string) $id => $name])
                            ->all();
                    })
                    ->getOptionLabelUsing(fn ($value): ?string => User::query()->whereKey($value)->value('name'))
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('logo')
                    ->default(null),

                Toggle::make('is_active')
                    ->required(),

                Toggle::make('is_main_branch')
                    ->required(),
            ]);
    }

    protected static function resolveEditedBranch(): ?Branch
    {
        $record = request()->route('record');

        if ($record instanceof Branch) {
            return $record;
        }

        if (is_numeric($record)) {
            return Branch::query()->find((int) $record);
        }

        return null;
    }
}
