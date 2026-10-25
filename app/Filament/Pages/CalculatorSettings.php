<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Settings of the whole quote calculator: btw, volume discount, rounding and the disclaimer.
 */
class CalculatorSettings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string | \UnitEnum | null $navigationGroup = 'Offerte calculator';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Instellingen';

    protected static ?string $title = 'Instellingen calculator';

    protected static ?string $slug = 'calculator/instellingen';

    private const KEYS = ['vat_percentage', 'volume_discount_percentage', 'round_to', 'disclaimer'];

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(collect(self::KEYS)
            ->mapWithKeys(fn (string $key) => [$key => Setting::get("calculator.$key", config("calculator.$key"))])
            ->all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Rekenen')
                    ->columns(3)
                    ->schema([
                        TextInput::make('vat_percentage')
                            ->label('Btw')
                            ->numeric()->minValue(0)->maxValue(100)->suffix('%')->required(),
                        TextInput::make('volume_discount_percentage')
                            ->label('Staffelkorting vanaf de 2e boom')
                            ->helperText('Korting op het deel "per boom". 0 = geen korting.')
                            ->numeric()->minValue(0)->maxValue(100)->suffix('%')->required(),
                        TextInput::make('round_to')
                            ->label('Afronden op')
                            ->helperText('Bijvoorbeeld 25: € 437 wordt € 425.')
                            ->numeric()->minValue(1)->prefix('€')->required(),
                    ]),
                Section::make('Disclaimer')
                    ->description('Staat onder elke prijsindicatie op de website.')
                    ->schema([
                        Textarea::make('disclaimer')->hiddenLabel()->rows(5)->required(),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([$this->getFormContentComponent()]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')->label('Opslaan')->submit('save')->keyBindings(['mod+s']),
                ]),
            ]);
    }

    public function save(): void
    {
        foreach ($this->form->getState() as $key => $value) {
            Setting::put("calculator.$key", in_array($key, ['disclaimer'], true) ? $value : (float) $value);
        }

        Notification::make()->success()->title('Opgeslagen')->send();
    }
}
