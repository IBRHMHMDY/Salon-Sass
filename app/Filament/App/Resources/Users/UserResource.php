<?php

namespace App\Filament\App\Resources\Users;

use App\Filament\App\Resources\Users\Pages\CreateUser;
use App\Filament\App\Resources\Users\Pages\EditUser;
use App\Filament\App\Resources\Users\Pages\ListUsers;
use App\Filament\App\Resources\Users\RelationManagers\ServicesRelationManager;
use App\Filament\App\Resources\Users\Schemas\UserForm;
use App\Filament\App\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Staff';

    protected static ?string $recordTitleAttribute = 'Staff';

    protected static ?string $tenantOwnershipRelationshipName = 'branch';

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return ! static::hasRoleInsensitive($user, 'Employee');
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return ! static::hasRoleInsensitive($user, 'Employee');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $tenantBranchId = Filament::getTenant()?->id;
        $authenticatedUserId = Auth::id();
        $isEmployee = static::hasRoleInsensitive(Auth::user(), 'Employee');

        if (! $tenantBranchId) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->when($authenticatedUserId, fn (Builder $userQuery) => $userQuery->whereKeyNot($authenticatedUserId))
            ->where('branch_id', $tenantBranchId)
            ->whereHas('roles', fn (Builder $roleQuery) => $roleQuery->whereRaw('LOWER(name) IN (?, ?)', ['manager', 'employee']))
            ->when(
                $isEmployee,
                fn (Builder $userQuery) => $userQuery->whereDoesntHave('roles', fn (Builder $roleQuery) => $roleQuery->whereRaw('LOWER(name) = ?', ['manager']))
            );
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ServicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    protected static function hasRoleInsensitive($user, string $role): bool
    {
        if (! $user) {
            return false;
        }

        return $user->getRoleNames()->contains(
            fn (string $userRole): bool => strcasecmp($userRole, $role) === 0
        );
    }
}
