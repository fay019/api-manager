<?php

namespace App\Filament\Resources;

use App\Models\ApiKey;
use App\Services\ApiKeyService;
use BackedEnum;
use Filament\Actions\Action as FormAction;
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Key Information')
                    ->description('Basic details about this API key')
                    ->schema([
                        Select::make('api_client_id')
                            ->label('API Client')
                            ->relationship('apiClient', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('name')
                            ->label('Key Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Mobile App Key, Integration #1'),
                    ])->columns(2),

                Section::make('Validity & Status')
                    ->description('Control when this key is active')
                    ->schema([
                        DateTimePicker::make('starts_at')
                            ->label('Starts At')
                            ->default(now())
                            ->helperText('When this key becomes active'),

                        DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->helperText('Leave empty for no expiration'),

                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true)
                            ->helperText('Manually enable or disable this key'),
                    ])->columns(3),

                Section::make('Key Metadata')
                    ->description('Technical details about the key')
                    ->schema([
                        TextInput::make('full_key')
                            ->label('Clé API complète')
                            ->formatStateUsing(function ($record) {
                                if (! $record?->key_encrypted) {
                                    return null;
                                }

                                try {
                                    return Crypt::decryptString($record->key_encrypted);
                                } catch (\Exception $e) {
                                    return 'Error: Could not decrypt key';
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
                                                    alert('Clé copiée !');
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
                                                alert('Clé copiée !');
                                            }
                                            return false;
                                        ",
                                    ])
                            )
                            ->columnSpanFull(),

                        TextInput::make('key_prefix')
                            ->label('Key Prefix')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Generated after creation'),

                        DateTimePicker::make('last_used_at')
                            ->label('Last Used At')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Never'),
                    ])->columns(2)
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('apiClient.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Key Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M d, Y')
                    ->placeholder('Never')
                    ->sortable(),

                TextColumn::make('key_prefix')
                    ->label('Key Prefix')
                    ->formatStateUsing(fn ($state) => $state.'****'),

                TextColumn::make('is_active')
                    ->label('Status')
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
                    ->sortable(),

                TextColumn::make('last_used_at')
                    ->label('Last Used')
                    ->dateTime('M d, Y H:i')
                    ->placeholder('Never')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M d, Y')
                    ->placeholder('Never')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        1 => 'Active',
                        0 => 'Revoked',
                    ])
                    ->label('Status'),

                Tables\Filters\SelectFilter::make('api_client_id')
                    ->relationship('apiClient', 'name')
                    ->label('Client'),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('regenerate')
                    ->label('Régénérer')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Régénérer la clé API')
                    ->modalDescription('Êtes-vous sûr de vouloir régénérer cette clé ? L\'ancienne clé cessera immédiatement de fonctionner.')
                    ->modalSubmitActionLabel('Régénérer')
                    ->action(function (ApiKey $record) {
                        $generatedKey = (new ApiKeyService)->generateKey();

                        $record->update([
                            'key_encrypted' => $generatedKey['encrypted'],
                            'key_prefix' => $generatedKey['prefix'],
                            'is_active' => true,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Clé API régénérée')
                            ->body("Voici votre nouvelle clé API :  \n`{$generatedKey['raw']}`  \n\n**Note :** Conservez-la précieusement.")
                            ->persistent()
                            ->actions([
                                \Filament\Actions\Action::make('copy')
                                    ->label('Copier la clé')
                                    ->color('gray')
                                    ->url('#')
                                    ->extraAttributes([
                                        'onclick' => "
                                            const text = '{$generatedKey['raw']}';
                                            if (navigator.clipboard && window.isSecureContext) {
                                                navigator.clipboard.writeText(text).then(() => {
                                                    alert('Clé copiée !');
                                                }).catch(err => {
                                                    console.error('Erreur lors de la copie : ', err);
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
                                                    alert('Clé copiée !');
                                                } catch (err) {
                                                    console.error('Erreur lors de la copie (fallback) : ', err);
                                                }
                                                document.body.removeChild(textArea);
                                            }
                                            return false;
                                        ",
                                    ]),
                            ])
                            ->send();
                    }),

                \Filament\Actions\Action::make('revoke')
                    ->label('Revoke')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn (ApiKey $record) => $record->is_active)
                    ->action(function (ApiKey $record) {
                        $record->update(['is_active' => false]);
                        Notification::make()
                            ->success()
                            ->title('Key Revoked')
                            ->body('API key has been revoked.')
                            ->send();
                    })
                    ->requiresConfirmation(),

                \Filament\Actions\DeleteAction::make()
                    ->visible(fn (ApiKey $record) => ! $record->is_active),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ApiKeyResource\Pages\ListApiKeys::route('/'),
            'create' => \App\Filament\Resources\ApiKeyResource\Pages\CreateApiKey::route('/create'),
            'view' => \App\Filament\Resources\ApiKeyResource\Pages\ViewApiKey::route('/{record}'),
            'edit' => \App\Filament\Resources\ApiKeyResource\Pages\EditApiKey::route('/{record}/edit'),
        ];
    }
}
