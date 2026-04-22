<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.client.name'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('filament.client.email'))
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label(__('filament.client.is_active'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('apiClients_count')
                    ->label(__('filament.client.applications'))
                    ->counts('apiClients'),
                TextColumn::make('last_login_at')
                    ->label(__('filament.client.last_login'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('activated_at')
                    ->label(__('filament.client.activated_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
