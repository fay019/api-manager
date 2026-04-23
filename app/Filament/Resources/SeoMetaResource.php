<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoMetaResource\Pages;
use App\Models\SeoMeta;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class SeoMetaResource extends Resource
{
    protected static ?string $model = SeoMeta::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    public static function getModelLabel(): string
    {
        return __('filament.seo_meta.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.seo_meta.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make()->columns(2)->schema([
                TextInput::make('route_name')
                    ->label(__('filament.seo_meta.route_name'))
                    ->placeholder('home')
                    ->columnSpan(1),
                TextInput::make('url')
                    ->label(__('filament.seo_meta.url'))
                    ->placeholder('/')
                    ->columnSpan(1),
                TextInput::make('locale')
                    ->label(__('filament.seo_meta.locale'))
                    ->default('fr')
                    ->columnSpan(1),
            ]),
            Section::make(__('filament.seo_meta.section_seo'))->columns(2)->schema([
                TextInput::make('title')
                    ->label(__('filament.seo_meta.title_field'))
                    ->required(),
                TextInput::make('keywords')
                    ->label(__('filament.seo_meta.keywords')),
                Textarea::make('description')
                    ->label(__('filament.seo_meta.description'))
                    ->required()
                    ->rows(2),
                TextInput::make('canonical_url')
                    ->label(__('filament.seo_meta.canonical_url')),
            ]),
            Section::make(__('filament.seo_meta.section_og'))->columns(2)->schema([
                TextInput::make('og_title')
                    ->label(__('filament.seo_meta.og_title')),
                Textarea::make('og_description')
                    ->label(__('filament.seo_meta.og_description'))
                    ->rows(2),
                TextInput::make('og_image')
                    ->label(__('filament.seo_meta.og_image')),
            ]),
            Grid::make()->columns(3)->schema([
                TextInput::make('robots')
                    ->label(__('filament.seo_meta.robots'))
                    ->default('index,follow'),
                Toggle::make('is_auto_generated')
                    ->label(__('filament.seo_meta.is_auto_generated')),
                Toggle::make('is_ignored')
                    ->label(__('filament.seo_meta.is_ignored')),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('route_name')
                ->label(__('filament.seo_meta.route_name'))
                ->searchable(),
            TextColumn::make('url')
                ->label(__('filament.seo_meta.url'))
                ->searchable(),
            TextColumn::make('locale')
                ->label(__('filament.seo_meta.locale')),
            TextColumn::make('title')
                ->label(__('filament.seo_meta.title_field'))
                ->limit(30),
            ToggleColumn::make('is_auto_generated')
                ->label(__('filament.seo_meta.is_auto_generated')),
            ToggleColumn::make('is_ignored')
                ->label(__('filament.seo_meta.is_ignored')),
        ])->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeoMetas::route('/'),
            'create' => Pages\CreateSeoMeta::route('/create'),
            'edit' => Pages\EditSeoMeta::route('/{record}/edit'),
        ];
    }
}
