<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthorResource\Pages;
use App\Filament\Resources\AuthorResource\RelationManagers;
use App\Models\Author;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Cache;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->regex('/^.+@.+$/i')
                    ->unique(Author::class, 'email', ignoreRecord: true)
                    ->required(),
                TextInput::make('phone')
                    ->required()->rules([
                        function () {
                            return function (string $attribute, $value, Closure $fail) {
                                if (Str::length($value) != 10 || is_nan($value)) {
                                    $fail("The phone number is invalid.");
                                }
                            };
                        },
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable(),
                ToggleColumn::make('is_popular'),
                TextColumn::make('created_at')->date(),
            ])
            ->filters([
                SelectFilter::make('is_popular')
                ->options([
                    true => 'popular',
                ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()->after(function(){
                    Cache::forget('popularAuthorBooks');
                }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAuthors::route('/'),
            // 'create' => Pages\CreateAuthor::route('/create'),
            // 'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
