<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Filament\Shared\Pages\CreateRecordRedirectToIndex;

class CreateUser extends CreateRecordRedirectToIndex
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        /** @var User $user */
        $user = $this->record;

        // تعيين دور Owner تلقائياً
        $user->assignRole('Owner');
    }
}
