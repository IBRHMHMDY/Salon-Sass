<?php

namespace App\Filament\App\Resources\Branches\Pages;

use App\Filament\App\Resources\Branches\BranchResource;
use App\Filament\Shared\Pages\EditRecordRedirectToIndex;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;

class EditBranch extends EditRecordRedirectToIndex
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
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
