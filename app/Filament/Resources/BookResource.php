<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Card::make()
                            ->schema([
                                TextInput::make('title')
                                    ->unique(Book::class, 'slug', ignoreRecord: true)
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                                    ->required(),
                                TextInput::make('slug')
                                    ->unique(Book::class, 'slug', ignoreRecord: true)
                                    ->required()
                                    ->disabled(),
                                RichEditor::make('description')->required()->columns(1),
                                FileUpload::make('cover_image')->directory('book-thumbnail')->required(),
                                FileUpload::make('book')->acceptedFileTypes(['application/pdf'])->required(),
                            ])

                            ->columnSpan(2),
                        Card::make()
                            ->schema([
                                TextInput::make('price')
                                    ->required()->rules([
                                        function () {
                                            return function (string $attribute, $value, Closure $fail) {
                                                if (is_nan($value)) {
                                                    $fail("Please enter number only.");
                                                }
                                            };
                                        },
                                    ]),
                                DatePicker::make('publication_date')->required(),
                                Section::make('Author')
                                    ->schema([
                                        Select::make('author_id')
                                            ->relationship('author', 'name')
                                            ->required()
                                            ->createOptionForm([
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
                                            ]),
                                    ])
                                    ->columns(1),
                                Section::make('Genre')
                                    ->schema([
                                        Select::make('genre_id')
                                            ->relationship('genre', 'name')
                                            ->required()
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->unique(Genre::class, 'slug', ignoreRecord: true)
                                                    ->reactive()
                                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                                                    ->required(),
                                                TextInput::make('slug')
                                                    ->unique(Genre::class, 'slug', ignoreRecord: true)
                                                    ->required()
                                                    ->disabled(),
                                            ]),
                                    ])
                                    ->columns(1)


                            ])
                            ->columnSpan(1),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image'),
                TextColumn::make('created_at')->date(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('isbn')->searchable(),
                TextColumn::make('price')->prefix('₹')->searchable(),
                TextColumn::make('author.name')->searchable(),
                TextColumn::make('genre.name')->searchable(),
                ToggleColumn::make('is_popular'),
                TextColumn::make('book')->getStateUsing(function (Book $record) {
                    return new HtmlString('<a href="' . env('APP_ULR') . '/storage/' . $record->book . '" target="_blank"> Download </a>');
                })->color('primary')
            ])
            ->filters([
                SelectFilter::make('is_popular')
                    ->options([
                        true => 'popular',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
