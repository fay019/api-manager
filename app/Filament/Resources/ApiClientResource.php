<?php

namespace App\Filament\Resources;

use App\Models\ApiClient;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class ApiClientResource extends Resource
{
    protected static ?string $model = ApiClient::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static string|UnitEnum|null $navigationGroup = 'API Management';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Information')
                    ->description('Basic details about this API client')
                    ->schema([
                        TextInput::make('name')
                            ->label('Client Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Mobile App, Web Dashboard'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Select::make('client_type')
                            ->label('Client Type')
                            ->options([
                                'MOBILE' => 'Mobile Application',
                                'WEB' => 'Web Application',
                                'PARTNER' => 'External Partner',
                                'INTERNAL' => 'Internal Service',
                            ])
                            ->nullable(),

                        DatePicker::make('activated_at')
                            ->label('Activated At')
                            ->nullable(),
                    ])->columns(2),

                Section::make('Contact Details')
                    ->description('Who is responsible for this client')
                    ->schema([
                        TextInput::make('contact_name')
                            ->label('Contact Name')
                            ->maxLength(255),

                        TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('website')
                            ->label('Website URL')
                            ->url()
                            ->maxLength(255),
                    ])->columns(3),

                Section::make('Technical Configuration')
                    ->description('Rate limiting and origin control')
                    ->schema([
                        TextInput::make('rate_limit_per_minute')
                            ->label('Rate Limit (min)')
                            ->numeric()
                            ->minValue(1)
                            ->default(60)
                            ->suffix('req/min'),

                        TextInput::make('monthly_quota')
                            ->label('Monthly Quota')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Unlimited'),

                        TextInput::make('webhook_url')
                            ->label('Webhook URL')
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TagsInput::make('allowed_origins')
                            ->label('Allowed Origins')
                            ->placeholder('Add domains (e.g., https://example.com)')
                            ->helperText('Leave empty to allow all origins')
                            ->separator(',')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('About')
                    ->schema([
                        Textarea::make('description')
                            ->label('Client Description')
                            ->placeholder('What is this client for?')
                            ->rows(3),

                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->placeholder('Internal notes about this client...')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Client Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Disabled')
                    ->sortable(),

                TextColumn::make('client_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('contact_email')
                    ->label('Contact')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('rate_limit_per_minute')
                    ->label('Rate Limit')
                    ->suffix(' req/min')
                    ->sortable(),

                TextColumn::make('apiKeys_count')
                    ->label('API Keys')
                    ->counts('apiKeys')
                    ->sortable(),

                TextColumn::make('requestLogs_count')
                    ->label('Requests')
                    ->counts('requestLogs')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\ApiClientResource\RelationManagers\ApiKeysRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ApiClientResource\Pages\ListApiClients::route('/'),
            'create' => \App\Filament\Resources\ApiClientResource\Pages\CreateApiClient::route('/create'),
            'edit' => \App\Filament\Resources\ApiClientResource\Pages\EditApiClient::route('/{record}/edit'),
        ];
    }
}
