<?php

namespace App\Filament\Resources\Promos\Schemas;

use App\Enums\PromoStatus;
use App\Models\Promo;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class PromoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Configuration du Slug')
                    ->schema([
                        TextInput::make('slug')
                            ->label('Slug / Public Path')
                            ->unique(Promo::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->maxLength(255)
                            ->required()
                            ->helperText('Utilisé pour l\'URL publique. Ex: banner-hiver.'),
                    ]),

                Tabs::make('Contenu Multilingue')
                    ->tabs([
                        self::getLocaleTab('fr', 'Français'),
                        self::getLocaleTab('en', 'English'),
                        self::getLocaleTab('de', 'Deutsch'),
                        self::getLocaleTab('ar', 'العربية', 'rtl'),
                    ])
                    ->columnSpanFull(),

                Section::make('Apparence & Action')
                    ->schema([
                        Placeholder::make('public_url')
                            ->label('URL de l\'API')
                            ->content(function (?Promo $record) {
                                if (! $record) {
                                    return 'L\'URL sera générée après la création.';
                                }

                                try {
                                    $url = $record->slug
                                        ? route('api.v1.promo.by-slug', ['slug' => $record->slug])
                                        : route('api.v1.promo.banner');
                                } catch (\Exception $e) {
                                    $baseUrl = config('app.url');
                                    $url = $record->slug
                                        ? "{$baseUrl}/api/v1/promo/{$record->slug}.json"
                                        : "{$baseUrl}/api/v1/promo/banner.json";
                                }

                                return new HtmlString("
                                    <div class='flex items-center gap-2'>
                                        <code class='p-1 bg-gray-100 dark:bg-gray-800 rounded text-sm break-all'>{$url}</code>
                                        <button
                                            type='button'
                                            onclick='navigator.clipboard.writeText(\"{$url}\"); window.Filament.notifications.show({title: \"URL copiée !\", type: \"success\"})'
                                            class='p-1 text-gray-500 hover:text-primary-600 transition-colors'
                                            title='Copier l\'URL'
                                        >
                                            <svg class='w-5 h-5' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3'></path></svg>
                                        </button>
                                    </div>
                                ");
                            })
                            ->helperText('C\'est l\'URL à utiliser dans votre intégration frontend.'),
                    ]),

                Section::make('Paramètres & Statut')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('status')
                                    ->label('Statut')
                                    ->options(PromoStatus::class)
                                    ->required()
                                    ->default(PromoStatus::DRAFT)
                                    ->live()
                                    ->helperText('Le statut est mis à jour automatiquement en fonction des dates sélectionnées (sauf en mode Brouillon).'),
                                DateTimePicker::make('starts_at')
                                    ->label('Date de début')
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function (callable $set, callable $get, $livewire) {
                                        self::updateStatusBasedOnDates($set, $get);
                                        $livewire->validateOnly('data.ends_at');
                                    }),
                                DateTimePicker::make('ends_at')
                                    ->label('Date de fin')
                                    ->native(false)
                                    ->afterOrEqual('starts_at')
                                    ->validationMessages([
                                        'after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
                                    ])
                                    ->live()
                                    ->afterStateUpdated(function (callable $set, callable $get, $livewire) {
                                        self::updateStatusBasedOnDates($set, $get);
                                        $livewire->validateOnly('data.ends_at');
                                    }),
                            ]),
                        Slider::make('priority')
                            ->label('Priorité')
                            ->required()
                            ->minValue(1)
                            ->maxValue(10)
                            ->fillTrack()
                            ->step(1)
                            ->pips(PipsMode::Steps)
                            ->decimalPlaces(0)
                            ->default(1)
                            ->tooltips()
                            ->helperText('10 est le plus prioritaire, 1 le moins.'),
                        Grid::make(3)
                            ->schema([
                                Select::make('display_mode')
                                    ->label('Mode d\'affichage')
                                    ->options([
                                        'fixed_count' => 'Nombre fixe de vues',
                                        'unlimited' => 'Illimité',
                                        'once_per_day' => 'Une fois par jour',
                                        'once_per_week' => 'Une fois par semaine',
                                    ])
                                    ->default('fixed_count')
                                    ->required()
                                    ->live(),
                                TextInput::make('max_impressions')
                                    ->label('Impressions Max')
                                    ->numeric()
                                    ->default(9999)
                                    ->required()
                                    ->visible(fn ($get) => $get('display_mode') === 'fixed_count'),
                                TextInput::make('cooldown_seconds')
                                    ->label('Délai après fermeture (sec)')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->helperText('86400 = 24h'),
                            ]),
                    ]),

                Section::make('Média')
                    ->schema([
                        FileUpload::make('image_url')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('promos')
                            ->visibility('public'),
                    ]),

                TextInput::make('created_by')
                    ->hidden()
                    ->default(fn () => Auth::id())
                    ->required(),
            ]);
    }

    protected static function updateStatusBasedOnDates(callable $set, callable $get): void
    {
        $status = $get('status');
        $startsAtRaw = $get('starts_at');
        $endsAtRaw = $get('ends_at');

        // Si aucune date n'est définie, on repasse TOUJOURS en Brouillon
        if (! $startsAtRaw && ! $endsAtRaw) {
            $set('status', PromoStatus::DRAFT->value);

            return;
        }

        // Si on est déjà en brouillon mais qu'on vient de mettre des dates,
        // on ne change rien automatiquement pour laisser l'admin décider de publier.
        if ($status === PromoStatus::DRAFT->value) {
            return;
        }

        $startsAt = $startsAtRaw ? \Illuminate\Support\Carbon::parse($startsAtRaw) : null;
        $endsAt = $endsAtRaw ? \Illuminate\Support\Carbon::parse($endsAtRaw) : null;
        $now = now();

        // 1. Archivé : Si la date de fin est passée
        if ($endsAt && $endsAt->isPast()) {
            $set('status', PromoStatus::ARCHIVED->value);

            return;
        }

        // 2. Programmé : Si la date de début est dans le futur
        if ($startsAt && $startsAt->isFuture()) {
            $set('status', PromoStatus::SCHEDULED->value);

            return;
        }

        // 3. Publié : Si on est dans la période valide ou si pas de dates restrictives
        $set('status', PromoStatus::PUBLISHED->value);
    }

    protected static function getLocaleTab(string $locale, string $label, string $direction = 'ltr'): Tab
    {
        return Tab::make($locale)
            ->label($label)
            ->schema([
                TextInput::make("title.{$locale}")
                    ->label("Titre ({$label})")
                    ->required(fn () => $locale === 'fr')
                    ->maxLength(255)
                    ->extraInputAttributes(['dir' => $direction]),

                RichEditor::make("content.{$locale}")
                    ->label("Contenu ({$label})")
                    ->required(fn () => $locale === 'fr')
                    ->columnSpanFull()
                    ->extraAttributes(['dir' => $direction]),

                Grid::make(2)
                    ->schema([
                        TextInput::make("cta_text.{$locale}")
                            ->label("Texte du bouton ({$label})")
                            ->maxLength(255)
                            ->extraInputAttributes(['dir' => $direction]),

                        TextInput::make('cta_url')
                            ->label('Lien du bouton (URL globale)')
                            ->url()
                            ->maxLength(255)
                            ->helperText('L\'URL est généralement la même pour toutes les langues.'),
                    ]),
            ]);
    }
}
