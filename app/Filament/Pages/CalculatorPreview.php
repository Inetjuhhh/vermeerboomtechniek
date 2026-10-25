<?php

namespace App\Filament\Pages;

use App\Calculator\QuoteCalculator;
use App\Models\CalculatorService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Try the quote calculator with the current amounts, and see how the sum is built up.
 */
class CalculatorPreview extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string | \UnitEnum | null $navigationGroup = 'Offerte calculator';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Rekensom testen';

    protected static ?string $title = 'Rekensom testen';

    protected static ?string $slug = 'calculator/testen';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $service = CalculatorService::query()->active()->first();

        $this->form->fill([
            'service' => $service?->id,
            'trees' => 1,
            'answers' => static::defaultAnswers($service?->id),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Grid::make(['default' => 1, 'lg' => 5])->schema([
                    Section::make('Antwoorden')
                        ->description('Zo vult een bezoeker de calculator in.')
                        ->columnSpan(['lg' => 2])
                        ->schema(fn (Get $get) => [
                            Select::make('service')
                                ->label('Dienst')
                                ->options(fn () => CalculatorService::query()->orderBy('sort')->pluck('name', 'id'))
                                ->selectablePlaceholder(false)
                                ->live()
                                ->afterStateUpdated(fn (?string $state, Set $set) => $set('answers', static::defaultAnswers($state))),
                            TextInput::make('trees')
                                ->label('Aantal bomen')
                                ->numeric()->minValue(1)->maxValue(100)
                                ->live(debounce: 400)
                                ->visible(fn () => static::service($get('service'))?->asks_tree_count),
                            ...static::questionFields($get('service')),
                        ]),
                    Section::make('Uitkomst')
                        ->columnSpan(['lg' => 3])
                        ->schema([
                            View::make('filament.pages.calculator-preview-result')
                                ->viewData(function (Get $get) {
                                    $service = static::service($get('service'));
                                    $calculator = app(QuoteCalculator::class);

                                    return [
                                        'service' => $service,
                                        'estimates' => $service ? $calculator->calculate($service, (array) $get('answers'), (int) $get('trees')) : [],
                                        'money' => fn (float $amount) => $calculator->money($amount),
                                    ];
                                }),
                        ]),
                ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([EmbeddedSchema::make('form')]);
    }

    /** @return list<Select> */
    private static function questionFields(int | string | null $serviceId): array
    {
        $service = static::service($serviceId);

        if (! $service || $service->contact_only) {
            return [];
        }

        return $service->questions->map(fn ($question) => Select::make("answers.{$question->id}")
            ->label($question->label)
            ->helperText($question->hint)
            ->options($question->options->pluck('label', 'id'))
            ->selectablePlaceholder(false)
            ->live())->all();
    }

    /** @return array<int, int> */
    private static function defaultAnswers(int | string | null $serviceId): array
    {
        return static::service($serviceId)?->questions
            ->mapWithKeys(fn ($question) => [$question->id => $question->defaultOption()?->id])
            ->all() ?? [];
    }

    private static function service(int | string | null $id): ?CalculatorService
    {
        return $id ? once(fn () => CalculatorService::with(['questions.options', 'priceModels'])->find($id)) : null;
    }
}
