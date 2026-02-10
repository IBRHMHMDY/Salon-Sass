<?php

namespace App\Filament\App\Pages;

use App\Models\Branch;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class BranchInformation extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInformationCircle;

    protected static ?string $title = 'معلومات الفرع';

    protected static ?string $navigationLabel = 'معلومات الفرع';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'branch-information';

    protected string $view = 'filament.app.pages.branch-information';

    public static function canAccess(): bool
    {
        return Filament::getTenant() instanceof Branch;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $branch = $this->getCurrentBranch();

        if (! $branch) {
            return [
                'branch' => null,
                'details' => [],
            ];
        }

        return [
            'branch' => $branch,
            'details' => [
                ['label' => 'اسم الفرع', 'value' => $branch->name ?: '-'],
                ['label' => 'العنوان', 'value' => $branch->address ?: '-'],
                ['label' => 'اسم المدير', 'value' => $this->getManagerNames($branch)],
                ['label' => 'عدد موظفي الفرع', 'value' => (string) $this->getEmployeesCount($branch)],
                ['label' => 'تاريخ الإنشاء', 'value' => $branch->created_at?->format('Y-m-d H:i') ?? '-'],
                ['label' => 'رقم التليفون', 'value' => $branch->phone ?: '-'],
            ],
        ];
    }

    protected function getCurrentBranch(): ?Branch
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Branch) {
            return null;
        }

        return $tenant->loadMissing(['managers', 'manager']);
    }

    protected function getManagerNames(Branch $branch): string
    {
        $managerNames = $branch->managers->pluck('name');

        if (filled($branch->manager?->name)) {
            $managerNames->push($branch->manager->name);
        }

        $names = $managerNames
            ->filter()
            ->unique()
            ->values();

        if ($names->isEmpty()) {
            return '-';
        }

        return $names->implode('، ');
    }

    protected function getEmployeesCount(Branch $branch): int
    {
        return User::query()
            ->where('branch_id', $branch->id)
            ->whereHas(
                'roles',
                fn (Builder $query) => $query->whereRaw('LOWER(name) = ?', ['employee'])
            )
            ->count();
    }
}
