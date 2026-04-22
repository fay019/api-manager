<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.contact.section_message'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('filament.contact.name'))
                                    ->disabled(),

                                TextInput::make('email')
                                    ->label(__('filament.contact.email'))
                                    ->email()
                                    ->disabled(),
                            ]),

                        TextInput::make('subject')
                            ->label(__('filament.contact.subject'))
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->label(__('filament.contact.message'))
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.contact.reply_modal_title'))
                    ->schema([
                        Textarea::make('reply_message')
                            ->label(__('filament.contact.reply_message'))
                            ->disabled()
                            ->rows(4)
                            ->columnSpanFull(),

                        TextInput::make('replied_by')
                            ->label(__('filament.contact.replied_by'))
                            ->disabled(),

                        TextInput::make('replied_at')
                            ->label(__('filament.contact.replied_at'))
                            ->disabled(),
                    ])
                    ->visible(fn (string $operation, $record) => $operation === 'edit' && $record?->replied_at !== null),

                Section::make(__('filament.contact.section_status'))
                    ->schema([
                        Select::make('status')
                            ->label(__('filament.contact.status'))
                            ->options([
                                'new' => __('filament.contact.status_new'),
                                'read' => __('filament.contact.status_read'),
                                'replied' => __('filament.contact.status_replied'),
                                'spam' => __('filament.contact.status_spam'),
                            ])
                            ->required(),

                        Textarea::make('admin_notes')
                            ->label(__('filament.contact.admin_notes'))
                            ->rows(3)
                            ->placeholder(__('filament.contact.admin_notes_placeholder'))
                            ->columnSpanFull(),
                    ]),

                Section::make(__('filament.contact.section_technical'))
                    ->schema([
                        TextInput::make('language')
                            ->label(__('filament.contact.language'))
                            ->disabled(),

                        TextInput::make('ip_address')
                            ->label(__('filament.contact.ip_address'))
                            ->disabled(),

                        TextInput::make('created_at')
                            ->label(__('filament.contact.created_at'))
                            ->disabled(),

                        TextInput::make('honeypot_triggered')
                            ->label(__('filament.contact.honeypot_triggered'))
                            ->disabled()
                            ->formatStateUsing(fn (?bool $state) => $state ? __('filament.contact.yes') : __('filament.contact.no')),

                        TextInput::make('timestamp_check_valid')
                            ->label(__('filament.contact.timestamp_check_valid'))
                            ->disabled()
                            ->formatStateUsing(fn (?bool $state) => $state ? __('filament.contact.yes') : __('filament.contact.no')),
                    ])->columns(2),
            ]);
    }
}
