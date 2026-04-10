<?php

namespace App\Filament\Resources\Promos;

use App\Filament\Resources\Promos\Pages\CreatePromo;
use App\Filament\Resources\Promos\Pages\EditPromo;
use App\Filament\Resources\Promos\Pages\ListPromos;
use App\Filament\Resources\Promos\Schemas\PromoForm;
use App\Filament\Resources\Promos\Tables\PromosTable;
use App\Models\Promo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    public static function getModelLabel(): string
    {
        return __('filament.promos.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.promos.plural');
    }

    protected static ?string $recordTitleAttribute = 'title';

    public static function getRecordTitle(?Model $record): ?string
    {
        if (! $record instanceof Promo) {
            return null;
        }

        return $record->getTranslation('title') ?? parent::getRecordTitle($record);
    }

    public static function form(Schema $schema): Schema
    {
        return PromoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromos::route('/'),
            'create' => CreatePromo::route('/create'),
            'edit' => EditPromo::route('/{record}/edit'),
        ];
    }
}
