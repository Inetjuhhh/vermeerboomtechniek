<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalculatorOtherCostResource\Pages\CreateCalculatorOtherCost;
use App\Filament\Resources\CalculatorOtherCostResource\Pages\EditCalculatorOtherCost;
use App\Filament\Resources\CalculatorOtherCostResource\Pages\ListCalculatorOtherCosts;
use App\Models\CalculatorOtherCost;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CalculatorOtherCostResource extends Resource
{
    protected static ?string $model = CalculatorOtherCost::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string | \UnitEnum | null $navigationGroup = 'Offerte calculator';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Overige kosten';

    protected static ?string $modelLabel = 'overige kosten';

    protected static ?string $pluralModelLabel = 'overige kosten';

    protected static ?string $slug = 'calculator/overige-kosten';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->description('Kosten die bovenop het werk komen, zoals voorrijkosten. Ze staan apart in de berekening onder "Overige kosten". Bedragen exclusief btw.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->label('Naam')->required()->maxLength(100),
                        TextInput::make('description')->label('Uitleg')->maxLength(255),
                        TextInput::make('amount_min')->label('Van')->numeric()->minValue(0)->step(0.01)->prefix('€')->required()->default(0),
                        TextInput::make('amount_max')->label('Tot')->numeric()->minValue(0)->step(0.01)->prefix('€')->required()->default(0)->gte('amount_min'),
                        Toggle::make('per_tree')->label('Per boom')->helperText('Uit: één keer per opdracht.'),
                        Toggle::make('is_active')->label('Meerekenen')->default(true),
                        Toggle::make('applies_to_all')->label('Geldt voor alle diensten')->default(true)->live(),
                        CheckboxList::make('services')
                            ->label('Geldt voor')
                            ->relationship('services', 'name', fn ($query) => $query->orderBy('sort'))
                            ->columns(2)
                            ->required(fn (Get $get) => ! $get('applies_to_all'))
                            ->visible(fn (Get $get) => ! $get('applies_to_all'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')->label('Naam')->description(fn (CalculatorOtherCost $record) => $record->description),
                TextColumn::make('amount_min')
                    ->label('Bedrag')
                    ->formatStateUsing(fn (CalculatorOtherCost $record) => '€ ' . number_format($record->amount_min, 0, ',', '.') . ' – € ' . number_format($record->amount_max, 0, ',', '.') . ($record->per_tree ? ' per boom' : '')),
                TextColumn::make('applies_to_all')
                    ->label('Geldt voor')
                    ->formatStateUsing(fn (CalculatorOtherCost $record) => $record->applies_to_all ? 'Alle diensten' : $record->services->pluck('name')->join(', ')),
                ToggleColumn::make('is_active')->label('Meerekenen'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCalculatorOtherCosts::route('/'),
            'create' => CreateCalculatorOtherCost::route('/create'),
            'edit' => EditCalculatorOtherCost::route('/{record}/edit'),
        ];
    }
}
