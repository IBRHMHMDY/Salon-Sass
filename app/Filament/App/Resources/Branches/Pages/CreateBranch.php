<?php

namespace App\Filament\App\Resources\Branches\Pages;

use App\Filament\App\Resources\Branches\BranchResource;
use App\Filament\Shared\Pages\CreateRecordRedirectToIndex;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CreateBranch extends CreateRecordRedirectToIndex
{
    protected static string $resource = BranchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_id'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $managerId = $this->record?->manager_id;

        if (! $managerId || ! $this->record) {
            return;
        }

        User::query()
            ->whereKey($managerId)
            ->whereHas('roles', fn (Builder $query) => $query->whereRaw('LOWER(name) = ?', ['manager']))
            ->update([
                'branch_id' => $this->record->getKey(),
            ]);
    }
}
