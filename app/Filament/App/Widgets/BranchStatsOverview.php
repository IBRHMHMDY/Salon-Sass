<?php

namespace App\Filament\App\Widgets;

use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BranchStatsOverview extends BaseWidget
{
    protected ?string $heading = 'Branch Overview';

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Branch) {
            return [
                Stat::make('Staff', '0')
                    ->description('Select a branch from the tenant switcher')
                    ->color('gray')
                    ->icon('heroicon-o-users'),
                Stat::make('Branches', '0')
                    ->color('gray')
                    ->icon('heroicon-o-building-storefront'),
                Stat::make('Employees', '0')
                    ->color('gray')
                    ->icon('heroicon-o-user-group'),
                Stat::make('Assigned Services', '0')
                    ->color('gray')
                    ->icon('heroicon-o-rectangle-stack'),
            ];
        }

        $branchId = (int) $tenant->getKey();
        $salonId = (int) $tenant->salon_id;

        $staffQuery = User::query()
            ->where('branch_id', $branchId)
            ->whereHas(
                'roles',
                fn (Builder $roleQuery) => $roleQuery->whereRaw('LOWER(name) IN (?, ?)', ['manager', 'employee'])
            );

        $staffCount = (clone $staffQuery)->count();
        $employeesCount = (clone $staffQuery)
            ->whereHas('roles', fn (Builder $roleQuery) => $roleQuery->whereRaw('LOWER(name) = ?', ['employee']))
            ->count();
        $user = Auth::user();
        $ownerSalonIds = $user?->salons()->pluck('salons.id');
        $canSeeAllOwnedBranches = $user && $user->getRoleNames()->contains(
            fn (string $role): bool => strcasecmp($role, 'Owner') === 0
        );
        if ($ownerSalonIds?->isEmpty()) {
            $ownerSalonIds = collect([$salonId]);
        }
        $branchesCount = Branch::query()
            ->when(
                $canSeeAllOwnedBranches,
                fn (Builder $branchQuery): Builder => $branchQuery->where(function (Builder $ownerQuery) use ($ownerSalonIds, $user): Builder {
                    return $ownerQuery
                        ->where('owner_id', $user->id)
                        ->when(
                            $ownerSalonIds?->isNotEmpty(),
                            fn (Builder $nestedQuery): Builder => $nestedQuery->orWhereIn('salon_id', $ownerSalonIds->all()),
                        );
                }),
                fn (Builder $branchQuery): Builder => $branchQuery->where('salon_id', $salonId),
            )
            ->count();

        $assignedServicesCount = Service::query()
            ->where('salon_id', $salonId)
            ->whereHas(
                'users',
                fn (Builder $userQuery) => $userQuery->where('branch_id', $branchId)
            )
            ->distinct()
            ->count('services.id');

        return [
            Stat::make('Staff', (string) $staffCount)
                ->description("Branch: {$tenant->name}")
                ->color('primary')
                ->icon('heroicon-o-users'),
            Stat::make('Branches', (string) $branchesCount)
                ->description('Total branches available to you')
                ->color('info')
                ->icon('heroicon-o-building-storefront'),
            Stat::make('Employees', (string) $employeesCount)
                ->description('Role: Employee')
                ->color('success')
                ->icon('heroicon-o-user-group'),
            Stat::make('Assigned Services', (string) $assignedServicesCount)
                ->description('Services linked to this branch team')
                ->color('warning')
                ->icon('heroicon-o-rectangle-stack'),
        ];
    }
}
