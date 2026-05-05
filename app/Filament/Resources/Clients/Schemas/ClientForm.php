<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\Client;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // AUTHENTICATION & BASIC INFO
                Section::make(__('filament.client.section_account'))
                    ->description(__('filament.client.section_account_desc'))
                    ->schema([
                        Select::make('type')
                            ->label(__('filament.client.type'))
                            ->options([
                                'person' => __('filament.client.type_person'),
                                'company' => __('filament.client.type_company'),
                            ])
                            ->required()
                            ->live()
                            ->placeholder(__('filament.client.type_placeholder'))
                            ->columnSpan(['md' => 2]),

                        Toggle::make('is_active')
                            ->label(__('filament.client.active'))
                            ->default(true)
                            ->disabled(fn (Get $get) => ! $get('type'))
                            ->columnSpan(['md' => 1]),

                        TextInput::make('name')
                            ->label(__('filament.client.name'))
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText(__('filament.client.name_help'))
                            ->visible(fn (?Client $record) => $record !== null)
                            ->columnSpan(['md' => 1]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label(__('filament.client.first_name'))
                                    ->maxLength(255)
                                    ->required(fn (Get $get) => $get('type') === 'person')
                                    ->disabled(fn (Get $get) => ! $get('type'))
                                    ->visible(fn (Get $get) => $get('type') === 'person'),
                                TextInput::make('last_name')
                                    ->label(__('filament.client.last_name'))
                                    ->maxLength(255)
                                    ->required(fn (Get $get) => $get('type') === 'person')
                                    ->disabled(fn (Get $get) => ! $get('type'))
                                    ->visible(fn (Get $get) => $get('type') === 'person'),
                            ])
                            ->columnSpan(['md' => 2])
                            ->visible(fn (Get $get) => $get('type') === 'person'),

                        TextInput::make('company_name')
                            ->label(__('filament.client.company_name'))
                            ->maxLength(255)
                            ->required(fn (Get $get) => $get('type') === 'company')
                            ->disabled(fn (Get $get) => ! $get('type'))
                            ->visible(fn (Get $get) => $get('type') === 'company')
                            ->columnSpan(['md' => 2]),

                        TextInput::make('email')
                            ->label(__('filament.client.email'))
                            ->email()
                            ->maxLength(255)
                            ->required()
                            ->disabled(fn (Get $get) => ! $get('type'))
                            ->columnSpan(['md' => 2]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label(__('filament.client.password'))
                                    ->password()
                                    ->revealable()
                                    ->minLength(8)
                                    ->confirmed()
                                    ->disabled(fn (Get $get) => ! $get('type'))
                                    ->required(fn (?Client $record) => $record === null)
                                    ->hidden(fn (?Client $record) => $record !== null),

                                TextInput::make('password_confirmation')
                                    ->label(__('filament.client.password_confirmation'))
                                    ->password()
                                    ->revealable()
                                    ->minLength(8)
                                    ->disabled(fn (Get $get) => ! $get('type'))
                                    ->required(fn (?Client $record) => $record === null)
                                    ->hidden(fn (?Client $record) => $record !== null),
                            ])
                            ->columnSpan(['md' => 2])
                            ->visible(fn (?Client $record) => $record === null),
                    ])
                    ->columns(2),

                // CONTACT & BILLING
                Section::make(__('filament.client.section_contact_billing'))
                    ->description(__('filament.client.section_contact_billing_desc'))
                    ->disabled(fn (Get $get) => ! $get('type'))
                    ->schema([
                        Checkbox::make('same_contact_email')
                            ->label(__('filament.client.same_contact_email'))
                            ->live()
                            ->columnSpan(['md' => 2])
                            ->visible(fn (Get $get) => $get('type') === 'company'),

                        TextInput::make('contact_email')
                            ->label(__('filament.client.contact_email'))
                            ->email()
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('type') === 'company' && ! $get('same_contact_email'))
                            ->columnSpan(['md' => 1]),

                        TextInput::make('contact_name')
                            ->label(__('filament.client.contact_name'))
                            ->maxLength(255)
                            ->visible(fn (Get $get) => $get('type') === 'company')
                            ->columnSpan(['md' => 1]),

                        Checkbox::make('same_as_main_email')
                            ->label(__('filament.client.same_billing_email'))
                            ->live()
                            ->columnSpan(['md' => 2]),

                        TextInput::make('billing_email')
                            ->label(__('filament.client.billing_email'))
                            ->email()
                            ->maxLength(255)
                            ->visible(fn (Get $get) => ! $get('same_as_main_email'))
                            ->columnSpan(['md' => 2]),

                        Textarea::make('description')
                            ->label(__('filament.client.description'))
                            ->maxLength(1000)
                            ->rows(2)
                            ->visible(fn (Get $get) => $get('type') === 'company')
                            ->columnSpan(['md' => 2]),
                    ])
                    ->columns(2),

                // LOCATION & COORDINATES
                Section::make(__('filament.client.section_location'))
                    ->description(__('filament.client.section_location_desc'))
                    ->disabled(fn (Get $get) => ! $get('type'))
                    ->schema([
                        TextInput::make('phone')
                            ->label(__('filament.client.phone'))
                            ->tel()
                            ->maxLength(20)
                            ->columnSpan(['md' => 1]),

                        Select::make('country')
                            ->label(__('filament.client.country'))
                            ->options([
                                'DZ' => __('filament.countries.dz'),
                                'FR' => __('filament.countries.fr'),
                                'DE' => __('filament.countries.de'),
                                'GB' => __('filament.countries.gb'),
                                'IT' => __('filament.countries.it'),
                                'ES' => __('filament.countries.es'),
                                'NL' => __('filament.countries.nl'),
                                'BE' => __('filament.countries.be'),
                                'AT' => __('filament.countries.at'),
                                'CH' => __('filament.countries.ch'),
                                'SE' => __('filament.countries.se'),
                                'NO' => __('filament.countries.no'),
                                'DK' => __('filament.countries.dk'),
                                'FI' => __('filament.countries.fi'),
                                'PL' => __('filament.countries.pl'),
                                'CZ' => __('filament.countries.cz'),
                                'US' => __('filament.countries.us'),
                                'CA' => __('filament.countries.ca'),
                                'MX' => __('filament.countries.mx'),
                                'BR' => __('filament.countries.br'),
                                'AU' => __('filament.countries.au'),
                                'NZ' => __('filament.countries.nz'),
                                'JP' => __('filament.countries.jp'),
                                'CN' => __('filament.countries.cn'),
                                'IN' => __('filament.countries.in'),
                                'SG' => __('filament.countries.sg'),
                            ])
                            ->columnSpan(['md' => 1]),

                        TextInput::make('address_json.street')
                            ->label(__('filament.client.street'))
                            ->placeholder(__('filament.client.street_placeholder'))
                            ->maxLength(255)
                            ->columnSpan(['md' => 2]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('address_json.city')
                                    ->label(__('filament.client.city'))
                                    ->maxLength(255),
                                TextInput::make('address_json.postal_code')
                                    ->label(__('filament.client.postal_code'))
                                    ->maxLength(20),
                            ])
                            ->columnSpan(['md' => 2]),
                    ])
                    ->columns(2),

                // PREFERENCES
                Section::make(__('filament.client.section_preferences'))
                    ->description(__('filament.client.section_preferences_desc'))
                    ->disabled(fn (Get $get) => ! $get('type'))
                    ->schema([
                        Select::make('language')
                            ->label(__('filament.client.language'))
                            ->options([
                                'fr' => __('filament.languages.fr'),
                                'en' => __('filament.languages.en'),
                                'de' => __('filament.languages.de'),
                            ])
                            ->required()
                            ->columnSpan(['md' => 1]),

                        Select::make('timezone')
                            ->label(__('filament.client.timezone'))
                            ->searchable()
                            ->options(fn () => collect(timezone_identifiers_list())
                                ->mapWithKeys(fn ($tz) => [$tz => $tz])
                                ->toArray())
                            ->required()
                            ->columnSpan(['md' => 1]),
                    ])
                    ->columns(2),

                // SECURITY
                Section::make(__('filament.client.section_security'))
                    ->collapsible()
                    ->collapsed()
                    ->disabled(fn (Get $get) => ! $get('type'))
                    ->description(__('filament.client.section_security_desc'))
                    ->schema([
                        TextInput::make('failed_login_attempts')
                            ->label(__('filament.client.failed_login_attempts'))
                            ->numeric()
                            ->disabled()
                            ->columnSpan(['md' => 1]),

                        DateTimePicker::make('locked_until_at')
                            ->label(__('filament.client.locked_until'))
                            ->disabled()
                            ->columnSpan(['md' => 1]),

                        DateTimePicker::make('activated_at')
                            ->label(__('filament.client.activated_at'))
                            ->disabled()
                            ->columnSpan(['md' => 1]),

                        DateTimePicker::make('last_login_at')
                            ->label(__('filament.client.last_login'))
                            ->disabled()
                            ->columnSpan(['md' => 1]),
                    ])
                    ->columns(2),

                // ADMIN NOTES
                Section::make(__('filament.client.section_notes'))
                    ->collapsible()
                    ->collapsed()
                    ->disabled(fn (Get $get) => ! $get('type'))
                    ->description(__('filament.client.section_notes_desc'))
                    ->schema([
                        Textarea::make('notes')
                            ->label(__('filament.client.notes'))
                            ->rows(4)
                            ->columnSpan(['md' => 2]),
                    ])
                    ->columns(2),

                // AVATAR
                Section::make(__('filament.client.section_avatar'))
                    ->collapsible()
                    ->collapsed()
                    ->disabled(fn (Get $get) => ! $get('type'))
                    ->description(__('filament.client.section_avatar_desc'))
                    ->schema([
                        FileUpload::make('avatar')
                            ->label(__('filament.client.avatar'))
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->columnSpan(['md' => 1]),
                    ])
                    ->columns(2),
            ]);
    }
}
