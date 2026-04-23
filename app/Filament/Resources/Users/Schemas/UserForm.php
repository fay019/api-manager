<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.admin.section_info'))
                    ->description(fn (Get $get) => $get('id') === null
                        ? __('filament.admin.section_info_desc')
                        : __('filament.admin.section_info_desc_edit'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament.admin.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('filament.admin.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->label(__('filament.admin.password'))
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get) => $get('id') === null)
                            ->rules([Password::min(8)->mixedCase()->numbers()->symbols()])
                            ->maxLength(255),

                        TextInput::make('password_confirmation')
                            ->label(__('filament.admin.password_confirmation'))
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get) => $get('id') === null)
                            ->same('password')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make(__('filament.admin.section_permissions'))
                    ->description(__('filament.admin.section_permissions_desc'))
                    ->schema([
                        Checkbox::make('is_admin')
                            ->label(__('filament.admin.is_admin'))
                            ->helperText(__('filament.admin.is_admin_help')),
                    ]),
            ]);
    }
}
