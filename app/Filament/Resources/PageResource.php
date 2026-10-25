<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages\CreatePage;
use App\Filament\Resources\PageResource\Pages\EditPage;
use App\Filament\Resources\PageResource\Pages\ListPages;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\ToggleColumn;
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
                    Toggle::make('is_published')->label('Zichtbaar op de site')->default(true)
                        ->helperText('Uit: de pagina en alle links ernaar verdwijnen. Handig voor een tijdelijke of optionele pagina.'),
                    Toggle::make('show_in_menu')->label('In het menu en de footer')->live(),
                    TextInput::make('menu_label')->label('Naam in het menu')->maxLength(40)
                        ->helperText('Leeg: dan staat de titel in het menu.')
                        ->visible(fn (Get $get) => $get('show_in_menu')),
                    FileUpload::make('image')->label('Foto bovenaan')->image()->disk('public')->directory('paginas'),
                    TextInput::make('image_alt')->label('Omschrijving van de foto'),
                    Textarea::make('meta_description')->label('Omschrijving voor Google')->maxLength(300)->rows(3),
                ]),
                Section::make("Foto's onder de tekst")->columnSpanFull()->collapsible()
                    ->description("Bij foto's van internet: alleen met een vrije licentie (zoals CC BY of CC0), en vermeld de maker.")
                    ->schema([
                        Repeater::make('gallery')->hiddenLabel()->addActionLabel('Foto toevoegen')->reorderable()->collapsible()
                            ->itemLabel(fn (array $state) => $state['caption'] ?? null)
                            ->columns(2)
                            ->schema([
                                FileUpload::make('image')->label('Foto')->image()->disk('public')->directory('paginas')->required()->columnSpanFull(),
                                TextInput::make('caption')->label('Onderschrift'),
                                TextInput::make('credit')->label('Maker en licentie')->placeholder('bijv. Jan Jansen, CC BY 2.0'),
                                TextInput::make('credit_url')->label('Link naar de bron')->url()->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Titel'),
                TextColumn::make('slug')->label('Adres')->formatStateUsing(fn (string $state) => "/$state"),
                ToggleColumn::make('is_published')->label('Zichtbaar'),
                ToggleColumn::make('show_in_menu')->label('In menu'),
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
