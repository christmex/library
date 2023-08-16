<?php

namespace App\Filament\Resources\MemberResource\Pages;

use Filament\Actions;
use App\Imports\MemberImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\MemberResource;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // \Filament\Actions\Action::make('importMember')->color('success')
            // ->form([
            //     \Awcodes\Shout\Components\Shout::make('should-return-at')
            //         ->content('Download the template file before upload')
            //         ->type('info'),
            //     \Filament\Forms\Components\FileUpload::make('import_member')
            //         ->storeFiles(false)
            //         ->columnSpanFull(),
            // ])
            // ->action(function(array $data){
            //     DB::beginTransaction();
            //     try {
            //         Excel::import(new MemberImport, $data['import_member']);
            //         DB::commit();
            //         Notification::make()
            //             ->success()
            //             ->title('Member imported')
            //             ->send();
            //     } catch (\Throwable $th) {
            //         DB::rollback();
            //         Notification::make()
            //             ->danger()
            //             ->title($th->getMessage())
            //             ->send();
            //     }
            // })
        ];
    }
}
