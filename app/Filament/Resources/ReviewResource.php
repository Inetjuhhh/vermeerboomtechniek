<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages\CreateReview;
use App\Filament\Resources\ReviewResource\Pages\EditReview;
use App\Filament\Resources\ReviewResource\Pages\ListReviews;
use App\Models\Review;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string | \UnitEnum | null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'review';

    protected static ?string $pluralModelLabel = 'reviews';

    protected static ?string $slug = 'website/reviews';

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Textarea::make('quote')->label('Wat zei de klant?')->required()->rows(4)->columnSpanFull(),
            TextInput::make('name')->label('Naam')->helperText('Optioneel. Vraag eerst of dat mag.'),
            TextInput::make('place')->label('Plaats'),
            Toggle::make('is_active')->label('Zichtbaar')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->description('De bovenste zichtbare review staat op de homepagina.')
            ->columns([
                TextColumn::make('quote')->label('Review')->limit(90)->wrap(),
                TextColumn::make('name')->label('Naam'),
                ToggleColumn::make('is_active')->label('Zichtbaar'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'create' => CreateReview::route('/create'),
            'edit' => EditReview::route('/{record}/edit'),
        ];
    }
}
