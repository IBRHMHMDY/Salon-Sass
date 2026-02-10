<?php

namespace App\Filament\App\Resources\Services\Pages;

use App\Filament\App\Resources\Services\ServiceResource;
use App\Filament\Shared\Pages\CreateRecordRedirectToIndex;
use Filament\Facades\Filament;

class CreateService extends CreateRecordRedirectToIndex
{
    protected static string $resource = ServiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['salon_id'] = Filament::getTenant()?->salon_id;

        return $data;
    }
}
