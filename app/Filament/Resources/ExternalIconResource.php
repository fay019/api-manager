<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExternalIconResource\Pages;
use App\Models\ExternalIcon;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use UnitEnum;

class ExternalIconResource extends Resource
{
    protected static ?string $model = ExternalIcon::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-face-smile';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    public static function getModelLabel(): string
    {
        return __('filament.external_icon.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.external_icon.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__('filament.external_icon.name'))
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('slug')
                ->label(__('filament.external_icon.slug'))
                ->required()
                ->unique(ignoreRecord: true),
            Select::make('provider')
                ->label(__('filament.external_icon.provider'))
                ->options([
                    'heroicons' => 'Heroicons',
                    'lucide' => 'Lucide',
                    'devicon' => 'Devicon',
                    'custom' => 'Custom',
                ]),
            Select::make('type')
                ->label(__('filament.external_icon.type'))
                ->options(['svg' => 'SVG', 'cdn' => 'CDN'])
                ->required()
                ->live(),
            ColorPicker::make('color')
                ->label(__('filament.external_icon.color'))
                ->hint(__('filament.external_icon.color_hint'))
                ->visible(fn (Get $get): bool => $get('type') === 'svg'),
            Textarea::make('source')
                ->label(__('filament.external_icon.source'))
                ->required()
                ->rows(4),
            TagsInput::make('tags')
                ->label(__('filament.external_icon.tags')),
            Toggle::make('is_active')
                ->label(__('filament.external_icon.is_active'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ViewColumn::make('preview')
                ->label(__('filament.external_icon.preview'))
                ->view('filament.tables.columns.icon-preview'),
            TextColumn::make('name')
                ->label(__('filament.external_icon.name'))
                ->searchable(),
            TextColumn::make('slug')
                ->label(__('filament.external_icon.slug')),
            TextColumn::make('provider')
                ->label(__('filament.external_icon.provider')),
            TextColumn::make('type')
                ->label(__('filament.external_icon.type')),
            IconColumn::make('is_active')
                ->label(__('filament.external_icon.is_active'))
                ->boolean(),
        ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExternalIcons::route('/'),
            'create' => Pages\CreateExternalIcon::route('/create'),
            'edit' => Pages\EditExternalIcon::route('/{record}/edit'),
        ];
    }
}
