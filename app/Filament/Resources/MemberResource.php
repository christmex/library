<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Models\Department;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

    protected static ?string $navigationGroup = 'Member';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('department_id')
                    // ->options(Department::all()->pluck('department_name', 'id'))
                    ->relationship(name: 'department', titleAttribute: 'department_name')
                    ->createOptionForm([
                        Forms\Components\TextInput::make('department_name')
                            ->required()
                            ->unique(ignoreRecord: false)
                            ->maxLength(255),
                    ])
                    ->editOptionForm([
                        Forms\Components\TextInput::make('department_name')
                            ->required()
                            ->unique(ignoreRecord: false)
                            ->maxLength(255),
                    ])
                    ->searchable(),
                Forms\Components\TextInput::make('member_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('member_phone_number')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('member_profile_picture')
                    ->image()
                    ->imageEditor()
                    ->openable()
                    ->downloadable()
                    ->preserveFilenames()
                    ->directory('member-profile-picture')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('member_profile_picture')->size(80),
                Tables\Columns\TextColumn::make('member_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.department_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('member_phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => auth()->user()->id == 1), 
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }    
}
