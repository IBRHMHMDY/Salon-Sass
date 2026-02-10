<?php

namespace App\Filament\App\Resources\Users\Pages;

use App\Filament\App\Resources\Users\UserResource;
use App\Filament\Shared\Pages\CreateRecordRedirectToIndex;
use Filament\Facades\Filament;

class CreateUser extends CreateRecordRedirectToIndex
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $user = static::getModel()::create($data);

        $tenant = Filament::getTenant();

        if ($tenant?->salon_id) {
            $user->salons()->syncWithoutDetaching([$tenant->salon_id]);
        }

        return $user;
    }
}
