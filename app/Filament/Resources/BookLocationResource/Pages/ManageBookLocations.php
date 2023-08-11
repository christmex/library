<?php

namespace App\Filament\Resources\BookLocationResource\Pages;

use App\Filament\Resources\BookLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBookLocations extends ManageRecords
{
    protected static string $resource = BookLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
