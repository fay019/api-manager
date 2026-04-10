<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('filament.contact.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('name')
                    ->label(__('filament.contact.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('filament.contact.email'))
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('subject')
                    ->label(__('filament.contact.subject'))
                    ->searchable()
                    ->limit(50),

                BadgeColumn::make('status')
                    ->label(__('filament.contact.status'))
                    ->colors([
                        'info' => 'new',
                        'success' => 'replied',
                        'warning' => 'read',
                        'danger' => 'spam',
                    ])
                    ->formatStateUsing(fn (string $state): string => __("filament.contact.status_$state"))
                    ->sortable(),

                BadgeColumn::make('honeypot_triggered')
                    ->label(__('filament.contact.spam_detected'))
                    ->color(fn (bool $state): string => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn (bool $state): string => $state ? '⚠️ '.__('filament.contact.honeypot_triggered') : '✓')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('filament.contact.status'))
                    ->options([
                        'new' => __('filament.contact.status_new'),
                        'read' => __('filament.contact.status_read'),
                        'replied' => __('filament.contact.status_replied'),
                        'spam' => __('filament.contact.status_spam'),
                    ]),

                SelectFilter::make('honeypot_triggered')
                    ->label(__('filament.contact.spam_detected'))
                    ->options([
                        1 => __('filament.contact.yes'),
                        0 => __('filament.contact.no'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
