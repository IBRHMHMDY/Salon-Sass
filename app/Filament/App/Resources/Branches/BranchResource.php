<?php

namespace App\Filament\App\Resources\Branches;

use App\Filament\App\Resources\Branches\Pages\CreateBranch;
use App\Filament\App\Resources\Branches\Pages\EditBranch;
use App\Filament\App\Resources\Branches\Pages\ListBranches;
use App\Filament\App\Resources\Branches\RelationManagers\ManagersRelationManager;
use App\Filament\App\Resources\Branches\Schemas\BranchForm;
use App\Filament\App\Resources\Branches\Tables\BranchesTable;
use App\Models\Branch;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $tenantOwnershipRelationshipName = 'salon';

    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery()
            ->with(['managers', 'manager']);

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if (static::hasRoleInsensitive($user, 'Owner')) {
            $salonIds = $user->salons()->pluck('salons.id');

            $currentTenant = Filament::getTenant();

            if ($salonIds->isEmpty() && $currentTenant instanceof Branch) {
                $salonIds = collect([(int) $currentTenant->salon_id]);
            }

            return $query->where(function (Builder $ownerQuery) use ($salonIds, $user): Builder {
                return $ownerQuery
                    ->where('owner_id', $user->id)
                    ->when(
                        $salonIds->isNotEmpty(),
                        fn (Builder $nestedQuery): Builder => $nestedQuery->orWhereIn('salon_id', $salonIds->all()),
                    );
            });
        }

        if (static::hasRoleInsensitive($user, 'DevTest')) {
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccessBranches();
    }

    public static function canAccess(): bool
    {
        return static::canAccessBranches();
    }

    public static function canViewAny(): bool
    {
        return static::canAccessBranches();
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }

    protected static function canAccessBranches(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return static::hasRoleInsensitive($user, 'Owner')
            || static::hasRoleInsensitive($user, 'DevTest');
    }

    protected static function hasRoleInsensitive($user, string $role): bool
    {
        return $user->getRoleNames()->contains(
            fn (string $userRole): bool => strcasecmp($userRole, $role) === 0
        );
    }

    public static function form(Schema $schema): Schema
    {
        return BranchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BranchesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ManagersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBranches::route('/'),
            'create' => CreateBranch::route('/create'),
            'edit' => EditBranch::route('/{record}/edit'),
        ];
    }
}
