<?php

namespace App\Filament\Resources;

use App\Enums\OptionEffect;
use App\Filament\Resources\CalculatorServiceResource\Pages\CreateCalculatorService;
use App\Filament\Resources\CalculatorServiceResource\Pages\EditCalculatorService;
use App\Filament\Resources\CalculatorServiceResource\Pages\ListCalculatorServices;
use App\Models\CalculatorService;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CalculatorServiceResource extends Resource
{
    protected static ?string $model = CalculatorService::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string | \UnitEnum | null $navigationGroup = 'Offerte calculator';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Diensten en factoren';

    protected static ?string $modelLabel = 'dienst';

    protected static ?string $pluralModelLabel = 'diensten';

    protected static ?string $slug = 'calculator/diensten';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dienst')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Naam')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (?string $state, Set $set, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                        TextInput::make('slug')
                            ->label('Adres (slug)')
                            ->helperText('Wordt gebruikt in de link naar deze dienst in de calculator.')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash(),
                        TextInput::make('description')
                            ->label('Korte uitleg')
                            ->helperText('Staat onder de naam op de keuzeknop.')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Zichtbaar in de calculator')
                            ->default(true),
                        Toggle::make('asks_tree_count')
                            ->label('Vraag naar het aantal bomen')
                            ->helperText('Uit: er wordt met 1 boom gerekend.')
                            ->default(true),
                        Toggle::make('contact_only')
                            ->label('Geen prijs tonen, alleen "bel ons"')
                            ->helperText('Bijvoorbeeld bij stormschade.')
                            ->live(),
                        Textarea::make('contact_message')
                            ->label('Tekst in plaats van de prijs')
                            ->rows(3)
                            ->visible(fn (Get $get) => $get('contact_only'))
                            ->columnSpanFull(),
                    ]),

                Section::make('Rekenmethodes')
                    ->description('Hoe de basisprijs wordt opgebouwd. Heeft een dienst meer methodes, dan toont de calculator voor elke methode een schatting naast elkaar. Alle bedragen zijn exclusief btw.')
                    ->hidden(fn (Get $get) => $get('contact_only'))
                    ->schema([
                        Repeater::make('priceModels')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort')
                            ->collapsible()
                            ->itemLabel(fn (array $state) => $state['name'] ?? null)
                            ->addActionLabel('Rekenmethode toevoegen')
                            ->minItems(1)
                            ->defaultItems(1)
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 3])->schema([
                                    TextInput::make('name')->label('Naam')->required()->default('Prijs per boom'),
                                    TextInput::make('description')->label('Uitleg')->columnSpan(['md' => 2]),
                                ]),
                                Grid::make(['default' => 1, 'lg' => 3])->schema([
                                    Fieldset::make('Vaste prijs per opdracht')->columns(2)->columnSpan(1)->schema(static::range('base')),
                                    Fieldset::make('Prijs per boom')->columns(2)->columnSpan(1)->schema(static::range('per_tree')),
                                    Fieldset::make('Op uurbasis')->columns(2)->columnSpan(1)->schema([
                                        static::money('hourly_rate', 'Uurtarief')->columnSpanFull(),
                                        static::hours('hours_min', 'Uren vast, van'),
                                        static::hours('hours_max', 'tot')->gte('hours_min'),
                                        static::hours('hours_per_tree_min', 'Uren per boom, van'),
                                        static::hours('hours_per_tree_max', 'tot')->gte('hours_per_tree_min'),
                                    ]),
                                ]),
                                Toggle::make('is_active')->label('Meerekenen')->default(true),
                            ]),
                    ]),

                Section::make('Vragen en factoren')
                    ->description('Elke vraag heeft antwoorden. Een antwoord kan de prijs met een factor vermenigvuldigen, of een bedrag toevoegen. Het antwoord met "standaard" is vooraf aangevinkt.')
                    ->hidden(fn (Get $get) => $get('contact_only'))
                    ->schema([
                        Repeater::make('questions')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort')
                            ->collapsible()
                            ->collapsed()
                            ->itemLabel(fn (array $state) => $state['label'] ?? null)
                            ->addActionLabel('Vraag toevoegen')
                            ->schema([
                                TextInput::make('label')->label('Vraag')->required()->maxLength(255),
                                TextInput::make('hint')->label('Uitleg onder de vraag')->maxLength(500),
                                Repeater::make('options')
                                    ->label('Antwoorden')
                                    ->relationship()
                                    ->orderColumn('sort')
                                    ->minItems(1)
                                    ->addActionLabel('Antwoord toevoegen')
                                    ->columns(['default' => 2, 'lg' => 12])
                                    ->schema([
                                        TextInput::make('label')->label('Antwoord')->required()->columnSpan(['default' => 2, 'lg' => 4]),
                                        Select::make('effect')
                                            ->label('Effect op de prijs')
                                            ->options(OptionEffect::class)
                                            ->default(OptionEffect::None)
                                            ->required()
                                            ->selectablePlaceholder(false)
                                            ->live()
                                            ->columnSpan(['default' => 2, 'lg' => 3]),
                                        TextInput::make('factor')
                                            ->label('Factor')
                                            ->numeric()
                                            ->step(0.05)
                                            ->minValue(0)
                                            ->prefix('×')
                                            ->helperText('1,25 = 25% duurder')
                                            ->required(fn (Get $get) => static::effect($get) === OptionEffect::Factor)
                                            ->visible(fn (Get $get) => static::effect($get) === OptionEffect::Factor)
                                            ->columnSpan(['default' => 2, 'lg' => 4]),
                                        static::money('amount_min', 'Van')
                                            ->required(fn (Get $get) => static::effect($get)->usesAmount())
                                            ->visible(fn (Get $get) => static::effect($get)->usesAmount())
                                            ->columnSpan(['default' => 1, 'lg' => 2]),
                                        static::money('amount_max', 'Tot')
                                            ->gte('amount_min')
                                            ->required(fn (Get $get) => static::effect($get)->usesAmount())
                                            ->visible(fn (Get $get) => static::effect($get)->usesAmount())
                                            ->columnSpan(['default' => 1, 'lg' => 2]),
                                        Toggle::make('is_default')->label('Standaard')->inline(false)->columnSpan(['default' => 2, 'lg' => 1]),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')->label('Dienst')->description(fn (CalculatorService $record) => $record->description),
                TextColumn::make('price_models_count')->label('Rekenmethodes')->counts('priceModels'),
                TextColumn::make('questions_count')->label('Vragen')->counts('questions'),
                ToggleColumn::make('is_active')->label('Zichtbaar'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    /** @return list<TextInput> */
    private static function range(string $prefix): array
    {
        return [
            static::money("{$prefix}_min", 'Van'),
            static::money("{$prefix}_max", 'Tot')->gte("{$prefix}_min"),
        ];
    }

    private static function money(string $name, string $label): TextInput
    {
        return TextInput::make($name)->label($label)->numeric()->minValue(0)->step(0.01)->prefix('€')->default(0);
    }

    private static function hours(string $name, string $label): TextInput
    {
        return TextInput::make($name)->label($label)->numeric()->minValue(0)->step(0.25)->suffix('uur')->default(0);
    }

    private static function effect(Get $get): OptionEffect
    {
        $effect = $get('effect');

        return $effect instanceof OptionEffect ? $effect : (OptionEffect::tryFrom((string) $effect) ?? OptionEffect::None);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalculatorServices::route('/'),
            'create' => CreateCalculatorService::route('/create'),
            'edit' => EditCalculatorService::route('/{record}/edit'),
        ];
    }
}
