<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
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
                // GENERAL INFORMATION
                Section::make('General Information')
                    ->collapsible()
                    ->components([
                        Select::make('type')
                            ->label('Account Type')
                            ->options([
                                'person' => 'Person',
                                'company' => 'Company',
                            ])
                            ->required()
                            ->live(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('first_name')
                                    ->label('First Name')
                                    ->maxLength(255)
                                    ->required(fn (Get $get) => $get('type') === 'person')
                                    ->visible(fn (Get $get) => $get('type') === 'person'),
                                TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->maxLength(255)
                                    ->required(fn (Get $get) => $get('type') === 'person')
                                    ->visible(fn (Get $get) => $get('type') === 'person'),
                            ]),

                        TextInput::make('company_name')
                            ->label('Company Name')
                            ->maxLength(255)
                            ->required(fn (Get $get) => $get('type') === 'company')
                            ->visible(fn (Get $get) => $get('type') === 'company'),

                        TextInput::make('email')
                            ->label('Login Email')
                            ->email()
                            ->maxLength(255)
                            ->required(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        DateTimePicker::make('activated_at')
                            ->label('Activated At')
                            ->disabled(),

                        DateTimePicker::make('last_login_at')
                            ->label('Last Login')
                            ->disabled(),
                    ]),

                // COMPANY INFORMATION
                Section::make('Company Information')
                    ->collapsible()
                    ->visible(fn (Get $get) => $get('type') === 'company')
                    ->components([
                        Textarea::make('description')
                            ->label('Description')
                            ->maxLength(1000)
                            ->rows(3),
                    ]),

                // CONTACT INFORMATION
                Section::make('Contact Information')
                    ->collapsible()
                    ->visible(fn (Get $get) => $get('type') === 'company')
                    ->components([
                        Checkbox::make('same_contact_email')
                            ->label('Use main email as contact email'),

                        TextInput::make('contact_email')
                            ->label('Contact Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('contact_name')
                            ->label('Contact Person Name')
                            ->maxLength(255),
                    ]),

                // BILLING
                Section::make('Billing')
                    ->collapsible()
                    ->components([
                        Checkbox::make('same_as_main_email')
                            ->label('Use main email for billing'),

                        TextInput::make('billing_email')
                            ->label('Billing Email')
                            ->email()
                            ->maxLength(255),
                    ]),

                // COORDINATES
                Section::make('Coordinates')
                    ->collapsible()
                    ->components([
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('address_json.street')
                            ->label('Street Address')
                            ->placeholder('123 Main Street')
                            ->maxLength(255),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('address_json.city')
                                    ->label('City')
                                    ->maxLength(255),
                                TextInput::make('address_json.postal_code')
                                    ->label('Postal Code')
                                    ->maxLength(20),
                                Select::make('country')
                                    ->label('Country')
                                    ->options([
                                        'DZ' => 'Algeria',
                                        'FR' => 'France',
                                        'DE' => 'Germany',
                                        'GB' => 'United Kingdom',
                                        'IT' => 'Italy',
                                        'ES' => 'Spain',
                                        'NL' => 'Netherlands',
                                        'BE' => 'Belgium',
                                        'AT' => 'Austria',
                                        'CH' => 'Switzerland',
                                        'SE' => 'Sweden',
                                        'NO' => 'Norway',
                                        'DK' => 'Denmark',
                                        'FI' => 'Finland',
                                        'PL' => 'Poland',
                                        'CZ' => 'Czech Republic',
                                        'US' => 'United States',
                                        'CA' => 'Canada',
                                        'MX' => 'Mexico',
                                        'BR' => 'Brazil',
                                        'AU' => 'Australia',
                                        'NZ' => 'New Zealand',
                                        'JP' => 'Japan',
                                        'CN' => 'China',
                                        'IN' => 'India',
                                        'SG' => 'Singapore',
                                    ]),
                            ]),
                    ]),

                // PREFERENCES
                Section::make('Preferences')
                    ->collapsible()
                    ->components([
                        Select::make('language')
                            ->label('Language')
                            ->options([
                                'fr' => 'Français',
                                'en' => 'English',
                                'de' => 'Deutsch',
                            ])
                            ->required(),

                        Select::make('timezone')
                            ->label('Timezone')
                            ->searchable()
                            ->options(fn () => collect(timezone_identifiers_list())
                                ->mapWithKeys(fn ($tz) => [$tz => $tz])
                                ->toArray())
                            ->required(),
                    ]),

                // SECURITY (readonly)
                Section::make('Security')
                    ->collapsible()
                    ->components([
                        TextInput::make('failed_login_attempts')
                            ->label('Failed Login Attempts')
                            ->numeric()
                            ->disabled(),

                        DateTimePicker::make('locked_until_at')
                            ->label('Account Locked Until')
                            ->disabled(),
                    ]),

                // ADMIN NOTES
                Section::make('Admin Notes')
                    ->collapsible()
                    ->components([
                        Textarea::make('notes')
                            ->label('Internal Notes')
                            ->rows(4),
                    ]),

                // FILE UPLOAD
                Section::make('Avatar')
                    ->collapsible()
                    ->components([
                        FileUpload::make('avatar')
                            ->label('Avatar')
                            ->disk('public')
                            ->visibility('public'),
                    ]),
            ]);
    }
}
