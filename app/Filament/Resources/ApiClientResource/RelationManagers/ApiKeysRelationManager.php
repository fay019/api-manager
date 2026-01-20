<?php

namespace App\Filament\Resources\ApiClientResource\RelationManagers;

use App\Filament\Resources\ApiKeyResource;
use App\Models\ApiKey;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiKeysRelationManager extends RelationManager
{
    protected static string $relationship = 'apiKeys';

    protected static ?string $title = 'Clés API';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                DateTimePicker::make('starts_at')
                    ->label('Début'),
                DateTimePicker::make('expires_at')
                    ->label('Fin'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('key_prefix')
                    ->label('Préfixe')
                    ->formatStateUsing(fn($state) => $state . '****'),

                TextColumn::make('is_active')
                    ->label('Statut')
                    ->badge()
                    ->getStateUsing(fn(ApiKey $record): string => match(true) {
                        !$record->is_active => 'revoked',
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
                    }),

                TextColumn::make('last_used_at')
                    ->label('Dernière utilisation')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Jamais'),
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
                    ->url(fn (ApiKey $record): string => ApiKeyResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->url(fn (ApiKey $record): string => ApiKeyResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
