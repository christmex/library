<?php

namespace App\Filament\Resources\BookSourceResource\Pages;

use App\Filament\Resources\BookSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBookSources extends ManageRecords
{
    protected static string $resource = BookSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
