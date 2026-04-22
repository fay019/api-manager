<?php

namespace App\Filament\Resources\ApiClientResource\RelationManagers;

use App\Filament\Resources\ApiKeyResource;
use App\Models\ApiKey;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ApiKeysRelationManager extends RelationManager
{
    protected static string $relationship = 'apiKeys';

    protected static ?string $title = null;

    public static function getTitle(?Model $ownerRecord = null, string $pageClass = ''): string
    {
        return __('filament.key.plural');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filament.key.name'))
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label(__('filament.key.is_active'))
                    ->default(true),
                DateTimePicker::make('starts_at')
                    ->label(__('filament.key.starts_at')),
                DateTimePicker::make('expires_at')
                    ->label(__('filament.key.expires_at')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.key.name'))
                    ->searchable()
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
                    }),

                TextColumn::make('last_used_at')
                    ->label(__('filament.key.last_used'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder(__('filament.key.never')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // On ne permet pas la création directe ici car il y a une logique de génération de clé complexe
                // qui est gérée dans ApiKeyResource/Pages/CreateApiKey.php
            ])
            ->actions([
                ViewAction::make()
                    ->iconButton()
                    ->url(fn (ApiKey $record): string => ApiKeyResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->iconButton()
                    ->url(fn (ApiKey $record): string => ApiKeyResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
