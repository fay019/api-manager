<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiClientResource\Pages\CreateApiClient;
use App\Filament\Resources\ApiClientResource\Pages\EditApiClient;
use App\Filament\Resources\ApiClientResource\Pages\ListApiClients;
use App\Filament\Resources\ApiClientResource\RelationManagers\ApiKeysRelationManager;
use App\Models\ApiClient;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ApiClientResource extends Resource
{
    protected static ?string $model = ApiClient::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = null;

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.clients');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.client.plural');
    }

    protected static string|UnitEnum|null $navigationGroup = 'API Management';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.client.section_info'))
                    ->description(__('filament.client.section_info_desc'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament.client.name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder(__('filament.client.name_placeholder')),

                        Toggle::make('is_active')
                            ->label(__('filament.client.active'))
                            ->default(true),

                        Select::make('client_type')
                            ->label(__('filament.client.type'))
                            ->options([
                                'MOBILE' => __('filament.client.type_mobile'),
                                'WEB' => __('filament.client.type_web'),
                                'PARTNER' => __('filament.client.type_partner'),
                                'INTERNAL' => __('filament.client.type_internal'),
                            ])
                            ->nullable(),

                        DatePicker::make('activated_at')
                            ->label(__('filament.client.activated_at'))
                            ->nullable(),
                    ])->columns(2),

                Section::make(__('filament.client.section_contact'))
                    ->description(__('filament.client.section_contact_desc'))
                    ->schema([
                        TextInput::make('contact_name')
                            ->label(__('filament.client.contact_name'))
                            ->maxLength(255),

                        TextInput::make('contact_email')
                            ->label(__('filament.client.contact_email'))
                            ->email()
                            ->maxLength(255),

                        TextInput::make('website')
                            ->label(__('filament.client.website'))
                            ->url()
                            ->maxLength(255),
                    ])->columns(3),

                Section::make(__('filament.client.section_technical'))
                    ->description(__('filament.client.section_technical_desc'))
                    ->schema([
                        TextInput::make('rate_limit_per_minute')
                            ->label(__('filament.client.rate_limit'))
                            ->numeric()
                            ->minValue(1)
                            ->default(60)
                            ->suffix(__('filament.client.rate_limit_suffix')),

                        TextInput::make('monthly_quota')
                            ->label(__('filament.client.monthly_quota'))
                            ->numeric()
                            ->minValue(0)
                            ->placeholder(__('filament.client.monthly_quota_placeholder')),

                        TextInput::make('webhook_url')
                            ->label(__('filament.client.webhook_url'))
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TagsInput::make('allowed_origins')
                            ->label(__('filament.client.allowed_origins'))
                            ->placeholder(__('filament.client.allowed_origins_placeholder'))
                            ->helperText(__('filament.client.allowed_origins_help'))
                            ->separator(',')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make(__('filament.client.section_about'))
                    ->description(__('filament.client.section_about_desc'))
                    ->schema([
                        Textarea::make('description')
                            ->label(__('filament.client.description'))
                            ->placeholder(__('filament.client.description_placeholder'))
                            ->rows(3),

                        Textarea::make('notes')
                            ->label(__('filament.client.notes'))
                            ->placeholder(__('filament.client.notes_placeholder'))
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.client.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label(__('filament.client.status'))
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? __('filament.common.active') : __('filament.common.disabled'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('client_type')
                    ->label(__('filament.client.type'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('contact_email')
                    ->label(__('filament.client.contact'))
                    ->searchable()
                    ->toggleable()
                    ->toggleable(),

                TextColumn::make('rate_limit_per_minute')
                    ->label(__('filament.client.rate_limit'))
                    ->suffix(' req/min')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('apiKeys')
                    ->label(__('filament.key.plural'))
                    ->state(fn (ApiClient $record): string => sprintf(
                        '%d / %d',
                        $record->apiKeys()->where('is_active', true)->count(),
                        $record->apiKeys()->count()
                    ))
                    ->tooltip(fn (ApiClient $record): string => sprintf(
                        '%d active, %d inactive',
                        $record->apiKeys()->where('is_active', true)->count(),
                        $record->apiKeys()->where('is_active', false)->count()
                    ))
                    ->toggleable(),

                TextColumn::make('requestLogs')
                    ->label(__('filament.log.requests'))
                    ->state(fn (ApiClient $record): string => sprintf(
                        '%d / %d',
                        $record->requestLogs()->whereBetween('status_code', [200, 299])->count(),
                        $record->requestLogs()->count()
                    ))
                    ->tooltip(fn (ApiClient $record): string => sprintf(
                        '%d successful (2xx), %d failed (4xx/5xx)',
                        $record->requestLogs()->whereBetween('status_code', [200, 299])->count(),
                        $record->requestLogs()->whereBetween('status_code', [400, 599])->count()
                    ))
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('filament.common.created'))
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('filament.client.active')),
            ])
            ->actions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ApiKeysRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiClients::route('/'),
            'create' => CreateApiClient::route('/create'),
            'edit' => EditApiClient::route('/{record}/edit'),
        ];
    }
}
