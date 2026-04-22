<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.authentication'))
                    ->components([
                        TextInput::make('name')
                            ->label(__('filament.client.name'))
                            ->required(),
                        TextInput::make('email')
                            ->label(__('filament.client.email'))
                            ->email()
                            ->required(),
                        Toggle::make('is_active')
                            ->label(__('filament.client.is_active')),
                        DateTimePicker::make('activated_at')
                            ->label(__('filament.client.activated_at'))
                            ->disabled(),
                        DateTimePicker::make('last_login_at')
                            ->label(__('filament.client.last_login'))
                            ->disabled(),
                        TextInput::make('pending_email')
                            ->label(__('filament.pending_email'))
                            ->email()
                            ->disabled(),
                    ]),
                Section::make(__('filament.contact'))
                    ->components([
                        TextInput::make('contact_name')
                            ->label(__('filament.client.contact_name')),
                        TextInput::make('contact_email')
                            ->label(__('filament.client.contact_email'))
                            ->email(),
                        FileUpload::make('avatar')
                            ->label(__('filament.client.avatar'))
                            ->disk('public')
                            ->visibility('public'),
                    ]),
                Section::make(__('filament.admin'))
                    ->components([
                        Textarea::make('description')
                            ->label(__('filament.client.description')),
                        Textarea::make('notes')
                            ->label(__('filament.client.notes')),
                    ]),
            ]);
    }
}
