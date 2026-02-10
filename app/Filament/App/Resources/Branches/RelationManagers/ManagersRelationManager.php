<?php

namespace App\Filament\App\Resources\Branches\RelationManagers;

use App\Models\Branch;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ManagersRelationManager extends RelationManager
{
    protected static string $relationship = 'managers';

    protected static ?string $title = 'Managers';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        $user = Auth::user();

        if (! $user || ! $ownerRecord instanceof Branch) {
            return false;
        }

        return static::hasRoleInsensitive($user, 'Owner')
            && ((int) $ownerRecord->owner_id === (int) $user->id);
    }

    public function table(Table $table): Table
    {
        /** @var Branch $branch */
        $branch = $this->getOwnerRecord();

        return $table
            ->inverseRelationship('branch')
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->label('Assign manager')
                    ->recordTitleAttribute('name')
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query
                            ->whereHas('roles', fn (Builder $roleQuery) => $roleQuery->whereRaw('LOWER(name) = ?', ['manager']))
                            ->where(function (Builder $managerQuery) use ($branch): Builder {
                                return $managerQuery
                                    ->whereNull('branch_id')
                                    ->orWhereHas('branch', fn (Builder $branchQuery) => $branchQuery->where('salon_id', $branch->salon_id));
                            })
                            ->whereKeyNot(Auth::id())
                    ),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make()
                    ->label('Remove from branch'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                ]),
            ]);
    }

    protected static function hasRoleInsensitive($user, string $role): bool
    {
        return $user->getRoleNames()->contains(
            fn (string $userRole): bool => strcasecmp($userRole, $role) === 0
        );
    }
}
