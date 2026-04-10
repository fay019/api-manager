<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.user.section_info'))
                    ->description(fn (Get $get) => $get('id') === null
                        ? __('filament.user.section_info_desc')
                        : __('filament.user.section_info_desc_edit'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament.user.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('filament.user.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label(__('filament.user.password'))
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get) => $get('id') === null)
                            ->minLength(8)
                            ->maxLength(255),

                        TextInput::make('password_confirmation')
                            ->label(__('filament.user.password_confirmation'))
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get) => $get('id') === null)
                            ->same('password')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make(__('filament.user.section_permissions'))
                    ->description(__('filament.user.section_permissions_desc'))
                    ->schema([
                        Checkbox::make('is_admin')
                            ->label(__('filament.user.is_admin'))
                            ->helperText(__('filament.user.is_admin_help')),
                    ]),
            ]);
    }
}
