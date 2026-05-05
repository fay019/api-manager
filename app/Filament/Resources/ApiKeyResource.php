<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiKeyResource\Pages\CreateApiKey;
use App\Filament\Resources\ApiKeyResource\Pages\EditApiKey;
use App\Filament\Resources\ApiKeyResource\Pages\ListApiKeys;
use App\Filament\Resources\ApiKeyResource\Pages\ViewApiKey;
use App\Models\ApiKey;
use App\Services\ApiKeyService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Action as FormAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Crypt;
use UnitEnum;

class ApiKeyResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|UnitEnum|null $navigationGroup = 'API Management';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.keys');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.key.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.key.section_info'))
                    ->description(__('filament.key.section_info_desc') ?? 'Basic details about this API key')
                    ->schema([
                        Select::make('api_client_id')
                            ->label(__('filament.key.client'))
                            ->relationship('apiClient', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->label(__('filament.key.name'))
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Mobile App Key, Integration #1')
                            ->live(onBlur: true),

                        TextInput::make('slug')
                            ->label(__('filament.key.slug'))
                            ->disabled()
                            ->helperText(__('filament.key.slug_help')),
                    ])->columns(2),

                Section::make(__('filament.key.section_validity'))
                    ->description(__('filament.key.section_validity_desc') ?? 'Control when this key is active')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label(__('filament.key.starts_at'))
                            ->default(now())
                            ->helperText(__('filament.key.starts_at_help') ?? 'When this key becomes active'),

                        DateTimePicker::make('expires_at')
                            ->label(__('filament.key.expires_at'))
                            ->helperText(__('filament.key.expires_at_help') ?? 'Leave empty for no expiration'),

                        Toggle::make('is_active')
                            ->label(__('filament.key.is_active'))
                            ->default(true)
                            ->helperText(__('filament.key.is_active_help') ?? 'Manually enable or disable this key'),
                    ])->columns(3),

                Section::make(__('filament.key.section_metadata'))
                    ->description(__('filament.key.section_metadata_desc') ?? 'Technical details about the key')
                    ->schema([
                        TextInput::make('full_key')
                            ->label(__('filament.key.full_key'))
                            ->formatStateUsing(function ($record) {
                                if (! $record?->key_encrypted) {
                                    return null;
                                }

                                try {
                                    return Crypt::decryptString($record->key_encrypted);
                                } catch (\Exception $e) {
                                    return __('filament.key.decrypt_error') ?? 'Error: Could not decrypt key';
                                }
                            })
                            ->disabled()
                            ->dehydrated(false)
                            ->password()
                            ->revealable()
                            ->autocomplete('current-password')
                            ->suffixAction(
                                FormAction::make('copy_key')
                                    ->icon('heroicon-m-clipboard')
                                    ->action(function ($state) {
                                        // This is handled by Alpine/JS via extraAttributes
                                    })
                                    ->extraAttributes([
                                        'onclick' => "
                                            const input = this.closest('.fi-input-wrp')?.querySelector('input');
                                            const text = input ? input.value : '';
                                            if (!text) {
                                                console.error('Could not find input or value is empty');
                                                return false;
                                            }
                                            if (navigator.clipboard && window.isSecureContext) {
                                                navigator.clipboard.writeText(text).then(() => {
                                                    alert('".__('filament.key.copied')."');
                                                });
                                            } else {
                                                const textArea = document.createElement('textarea');
                                                textArea.value = text;
                                                textArea.style.position = 'fixed';
                                                textArea.style.left = '-9999px';
                                                document.body.appendChild(textArea);
                                                textArea.select();
                                                document.execCommand('copy');
                                                document.body.removeChild(textArea);
                                                alert('".__('filament.key.copied')."');
                                            }
                                            return false;
                                        ",
                                    ])
                            )
                            ->columnSpanFull(),

                        TextInput::make('key_prefix')
                            ->label(__('filament.key.prefix'))
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(__('filament.key.prefix_placeholder') ?? 'Generated after creation'),

                        DateTimePicker::make('last_used_at')
                            ->label(__('filament.key.last_used'))
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder(__('filament.key.never') ?? 'Never'),
                    ])->columns(2)
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('apiClient.name')
                    ->label(__('filament.key.client'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label(__('filament.key.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(__('filament.key.slug'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('starts_at')
                    ->label(__('filament.key.starts_at'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('expires_at')
                    ->label(__('filament.key.expires_at'))
                    ->dateTime('M d, Y')
                    ->placeholder(__('filament.key.never') ?? 'Never')
                    ->sortable(),

                TextColumn::make('key_prefix')
                    ->label(__('filament.key.prefix'))
                    ->formatStateUsing(fn ($state) => $state.'****'),

                TextColumn::make('is_active')
                    ->label(__('filament.key.status_active'))
                    ->badge()
                    ->getStateUsing(fn (ApiKey $record): string => match (true) {
                        ! $record->is_active => 'revoked',
                        $record->starts_at && $record->starts_at->isFuture() => 'scheduled',
                        $record->expires_at && $record->expires_at->isPast() => 'expired',
                        default => 'active',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'revoked' => 'danger',
                        'expired' => 'warning',
                        'scheduled' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'revoked' => __('filament.key.status_revoked'),
                        'scheduled' => __('filament.key.status_scheduled'),
                        'expired' => __('filament.key.status_expired'),
                        'active' => __('filament.key.status_active'),
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('last_used_at')
                    ->label(__('filament.key.last_used'))
                    ->dateTime('M d, Y H:i')
                    ->placeholder(__('filament.key.never') ?? 'Never')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('filament.common.created'))
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        1 => __('filament.key.filter_active'),
                        0 => __('filament.key.filter_revoked'),
                    ])
                    ->label(__('filament.key.status_active')),

                Tables\Filters\SelectFilter::make('api_client_id')
                    ->relationship('apiClient', 'name')
                    ->label(__('filament.key.filter_client')),
            ])
            ->actions([
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
                Action::make('regenerate')
                    ->label(__('filament.key.regenerate'))
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->iconButton()
                    ->requiresConfirmation()
                    ->modalHeading(__('filament.key.regenerate_action'))
                    ->modalDescription(__('filament.key.regenerate_confirm'))
                    ->modalSubmitActionLabel(__('filament.key.regenerate'))
                    ->action(function (ApiKey $record) {
                        $generatedKey = (new ApiKeyService)->generateKey();

                        $record->update([
                            'key_encrypted' => $generatedKey['encrypted'],
                            'key_prefix' => $generatedKey['prefix'],
                            'is_active' => true,
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('filament.key.regenerate_success'))
                            ->body(__('filament.key.regenerate_message')." \n`{$generatedKey['raw']}`")
                            ->persistent()
                            ->actions([
                                Action::make('copy')
                                    ->label(__('filament.key.copy_key'))
                                    ->color('gray')
                                    ->url('#')
                                    ->extraAttributes([
                                        'onclick' => "
                                            const text = '{$generatedKey['raw']}';
                                            if (navigator.clipboard && window.isSecureContext) {
                                                navigator.clipboard.writeText(text).then(() => {
                                                    alert('".__('filament.key.copied')."');
                                                }).catch(err => {
                                                    console.error('Error copying: ', err);
                                                });
                                            } else {
                                                const textArea = document.createElement('textarea');
                                                textArea.value = text;
                                                textArea.style.position = 'fixed';
                                                textArea.style.left = '-9999px';
                                                textArea.style.top = '0';
                                                document.body.appendChild(textArea);
                                                textArea.focus();
                                                textArea.select();
                                                try {
                                                    document.execCommand('copy');
                                                    alert('".__('filament.key.copied')."');
                                                } catch (err) {
                                                    console.error('Error copying (fallback): ', err);
                                                }
                                                document.body.removeChild(textArea);
                                            }
                                            return false;
                                        ",
                                    ]),
                            ])
                            ->send();
                    }),

                Action::make('revoke')
                    ->label(__('filament.key.revoke'))
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->iconButton()
                    ->visible(fn (ApiKey $record) => $record->is_active)
                    ->action(function (ApiKey $record) {
                        $record->update(['is_active' => false]);
                        Notification::make()
                            ->success()
                            ->title(__('filament.key.revoked'))
                            ->body(__('filament.key.revoke_success') ?? 'API key has been revoked.')
                            ->send();
                    })
                    ->requiresConfirmation(),

                DeleteAction::make()->iconButton()
                    ->visible(fn (ApiKey $record) => ! $record->is_active),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiKeys::route('/'),
            'create' => CreateApiKey::route('/create'),
            'view' => ViewApiKey::route('/{record}'),
            'edit' => EditApiKey::route('/{record}/edit'),
        ];
    }
}
