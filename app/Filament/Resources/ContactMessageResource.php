<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages\ListContactMessages;
use App\Models\ContactMessage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Berichten';

    protected static ?string $modelLabel = 'bericht';

    protected static ?string $pluralModelLabel = 'berichten';

    protected static ?string $slug = 'berichten';

    public static function getNavigationBadge(): ?string
    {
        $unread = ContactMessage::whereNull('read_at')->count();

        return $unread ? (string) $unread : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextEntry::make('name')->label('Naam'),
            TextEntry::make('created_at')->label('Ontvangen')->dateTime('j F Y, H:i'),
            TextEntry::make('email')->label('E-mail')->url(fn (ContactMessage $record) => 'mailto:' . $record->email),
            TextEntry::make('phone')->label('Telefoon')->placeholder('-'),
            TextEntry::make('subject')->label('Onderwerp')->placeholder('-')->columnSpanFull(),
            TextEntry::make('message')->label('Bericht')->columnSpanFull()->prose(),
            KeyValueEntry::make('calculator')->label('Uit de calculator')->columnSpanFull()
                ->keyLabel('Vraag')->valueLabel('Antwoord')
                ->getStateUsing(fn (ContactMessage $record) => static::summaryRows($record))
                ->visible(fn (ContactMessage $record) => filled($record->calculator_summary)),
        ]);
    }

    /** @return array<string, string> */
    private static function summaryRows(ContactMessage $record): array
    {
        $s = $record->calculator_summary;
        $rows = ['Dienst' => $s['service']] + ($s['trees'] ? ['Aantal bomen' => (string) $s['trees']] : []) + $s['answers'];
        foreach ($s['estimates'] as $e) {
            $rows["Indicatie {$e['name']}"] = "{$e['incl']} incl. btw ({$e['excl']} excl.)";
        }

        return $rows;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('read_at')->label('')->boolean()->getStateUsing(fn (ContactMessage $record) => $record->read_at === null)
                    ->trueIcon(Heroicon::Envelope)->falseIcon(Heroicon::OutlinedEnvelopeOpen)->trueColor('primary')->falseColor('gray')
                    ->tooltip(fn (ContactMessage $record) => $record->read_at ? 'Gelezen' : 'Nieuw'),
                TextColumn::make('name')->label('Naam')->searchable()->weight(fn (ContactMessage $record) => $record->read_at ? null : 'bold'),
                TextColumn::make('subject')->label('Onderwerp')->placeholder('-')->limit(50),
                IconColumn::make('calculator_summary')->label('Calculator')->boolean()->getStateUsing(fn (ContactMessage $record) => filled($record->calculator_summary)),
                TextColumn::make('created_at')->label('Ontvangen')->since()->sortable(),
            ])
            ->recordActions([
                ViewAction::make()->label('Lees')->after(fn (ContactMessage $record) => $record->read_at ?? $record->update(['read_at' => now()])),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListContactMessages::route('/')];
    }
}
