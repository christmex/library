<?php

namespace App\Filament\Resources\PenaltyResource\Pages;

use App\Filament\Resources\PenaltyResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePenalties extends ManageRecords
{
    protected static string $resource = PenaltyResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
