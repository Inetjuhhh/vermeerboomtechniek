<?php

namespace App\Filament\Resources;

use App\Enums\TreePart;
use App\Filament\Resources\ServiceResource\Pages\CreateService;
use App\Filament\Resources\ServiceResource\Pages\EditService;
use App\Filament\Resources\ServiceResource\Pages\ListServices;
use App\Models\Service;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string | \UnitEnum | null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'dienst';

    protected static ?string $pluralModelLabel = 'diensten';

    protected static ?string $slug = 'website/diensten';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make()->columnSpan(['lg' => 2])->schema([
                    TextInput::make('title')->label('Naam')->required()->maxLength(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (?string $state, Set $set, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                    TextInput::make('slug')->label('Adres')->prefix('/diensten/')->required()->alphaDash()->unique(ignoreRecord: true),
                    Textarea::make('summary')->label('Korte tekst')->helperText('Staat op de kaartjes op de homepagina en in het overzicht. Eén zin.')->required()->maxLength(300)->rows(2),
                    Textarea::make('intro')->label('Inleiding')->helperText('Staat bovenaan de pagina van de dienst. Leeg laten: dan staat de korte tekst er.')->rows(3),
                    RichEditor::make('body')->label('Tekst')->required()
                        ->toolbarButtons([['bold', 'italic', 'link'], ['h2', 'h3', 'blockquote'], ['bulletList', 'orderedList'], ['undo', 'redo']]),
                ]),
                Section::make()->columnSpan(['lg' => 1])->schema([
                    FileUpload::make('image')->label('Foto')->image()->disk('public')->directory('diensten')
                        ->helperText('Nu nog een tijdelijke foto van de oude site. Upload hier de echte foto.'),
                    TextInput::make('image_alt')->label('Omschrijving van de foto')->helperText('Voor blinden en Google, bijv. "Bas snoeit een eik".'),
                    Select::make('tree_part')->label('Deel van de boom')->options(TreePart::class)->required()->default(TreePart::Kroon)
                        ->helperText('In welke rij de dienst staat: kroon, stam of wortels.'),
                    Select::make('calculator_service_id')->label('Hoort bij calculator-dienst')
                        ->relationship('calculatorService', 'name')
                        ->helperText('Dan staat er een knop "Bereken je prijs" op de pagina.'),
                    Toggle::make('show_on_home')->label('Op de homepagina')->default(true),
                    Toggle::make('is_active')->label('Zichtbaar op de site')->default(true),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('image')->label('')->getStateUsing(fn (Service $record) => $record->imageUrl())->square(),
                TextColumn::make('title')->label('Dienst')->description(fn (Service $record) => $record->summary)->wrap(),
                TextColumn::make('tree_part')->label('Rij')->badge(),
                ToggleColumn::make('show_on_home')->label('Home'),
                ToggleColumn::make('is_active')->label('Zichtbaar'),
            ])
            ->recordActions([
                Action::make('view')->label('Bekijk')->icon(Heroicon::OutlinedEye)
                    ->url(fn (Service $record) => route('services.show', $record))->openUrlInNewTab(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
