<?php

namespace App\Filament\Resources\Promos\Tables;

use App\Enums\PromoStatus;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PromosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('image_url')
                    ->label('Image')
                    ->circular(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('Priorité')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Créé par')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(PromoStatus::class),
            ])
            ->defaultSort('priority', 'desc')
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
