<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string | \UnitEnum | null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'pagina';

    protected static ?string $pluralModelLabel = "pagina's";

    protected static ?string $slug = 'website/paginas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()->columnSpan(['lg' => 2])->schema([
                    TextInput::make('title')->label('Titel')->required(),
                    TextInput::make('slug')->label('Adres')->prefix('/')->required()->alphaDash()->unique(ignoreRecord: true)
                        ->notIn(['admin', 'diensten', 'contact', 'bereken-je-offerte', 'livewire', 'storage', 'up']),
                    Textarea::make('intro')->label('Inleiding')->rows(3),
                    RichEditor::make('body')->label('Tekst')
                        ->toolbarButtons([['bold', 'italic', 'link'], ['h2', 'h3', 'blockquote'], ['bulletList', 'orderedList'], ['undo', 'redo']]),
                ]),
                Section::make()->columnSpan(['lg' => 1])->schema([
                    FileUpload::make('image')->label('Foto')->image()->disk('public')->directory('paginas'),
                    TextInput::make('image_alt')->label('Omschrijving van de foto'),
                    Textarea::make('meta_description')->label('Omschrijving voor Google')->maxLength(300)->rows(3),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Titel'),
                TextColumn::make('slug')->label('Adres')->formatStateUsing(fn (string $state) => "/$state"),
            ])
            ->recordActions([
                Action::make('view')->label('Bekijk')->icon(Heroicon::OutlinedEye)
                    ->url(fn (Page $record) => route('page', $record))->openUrlInNewTab(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
