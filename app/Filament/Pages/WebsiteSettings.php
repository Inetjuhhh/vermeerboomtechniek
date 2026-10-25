<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\Site;
use Filament\Actions\Action;
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
 * Contact details shown in the header, footer, contact page and calculator.
 */
class WebsiteSettings extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string | \UnitEnum | null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Instellingen';

    protected static ?string $title = 'Instellingen website';

    protected static ?string $slug = 'website/instellingen';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(collect(Site::DEFAULTS)->keys()->mapWithKeys(fn ($key) => [$key => Site::get($key)])->all());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Contactgegevens')->columns(2)->schema([
                    TextInput::make('phone')->label('Telefoon')->required(),
                    TextInput::make('email')->label('E-mail')->email()->required()
                        ->helperText('Hier komen ook de berichten van het contactformulier binnen.'),
                    TextInput::make('street')->label('Straat en huisnummer')->required(),
                    TextInput::make('city')->label('Plaats')->required(),
                    TextInput::make('kvk')->label('KvK-nummer')->helperText('Staat onderaan elke pagina.'),
                    TextInput::make('youtube')->label('YouTube-video')->url()->helperText('De video "Een dag op pad" op de homepagina.'),
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
            Setting::put("site.$key", $value);
        }

        Notification::make()->success()->title('Opgeslagen')->send();
    }
}
