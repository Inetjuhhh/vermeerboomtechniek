<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificateResource\Pages\CreateCertificate;
use App\Filament\Resources\CertificateResource\Pages\EditCertificate;
use App\Filament\Resources\CertificateResource\Pages\ListCertificates;
use App\Models\Certificate;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string | \UnitEnum | null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'certificaat';

    protected static ?string $pluralModelLabel = 'certificaten';

    protected static ?string $slug = 'website/certificaten';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Naam')->required()->columnSpanFull(),
            TextInput::make('abbreviation')->label('Afkorting'),
            TextInput::make('year')->label('Behaald in')->numeric()->minValue(1990)->maxValue(2100),
            FileUpload::make('logo')->label('Logo')->image()->disk('public')->directory('certificaten'),
            Toggle::make('show_in_timeline')->label('In de tijdlijn op de homepagina')->helperText('Alleen als er ook een jaar is ingevuld.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('logo')->label('')->getStateUsing(fn (Certificate $record) => $record->imageUrl('logo')),
                TextColumn::make('name')->label('Certificaat')->wrap(),
                TextColumn::make('year')->label('Jaar'),
                ToggleColumn::make('show_in_timeline')->label('Tijdlijn'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertificates::route('/'),
            'create' => CreateCertificate::route('/create'),
            'edit' => EditCertificate::route('/{record}/edit'),
        ];
    }
}
