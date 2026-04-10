<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.user.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('filament.user.email'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                IconColumn::make('is_admin')
                    ->label(__('filament.user.is_admin'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('filament.common.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('filament.common.updated_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_admin')
                    ->label(__('filament.user.is_admin'))
                    ->options([
                        true => __('filament.common.active'),
                        false => __('filament.common.disabled'),
                    ]),
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
