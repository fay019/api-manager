<?php

namespace App\Filament\Resources\Promos\Schemas;

use App\Enums\PromoStatus;
use App\Models\Promo;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class PromoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament.promos.slug_section'))
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('filament.promos.slug_label'))
                            ->unique(Promo::class, 'slug', ignoreRecord: true)
                            ->rules(['alpha_dash'])
                            ->maxLength(255)
                            ->required()
                            ->helperText(__('filament.promos.slug_help')),
                    ]),

                Tabs::make(__('filament.promos.multilingual_content'))
                    ->tabs([
                        self::getLocaleTab('fr', __('filament.contact.lang_fr')),
                        self::getLocaleTab('en', __('filament.contact.lang_en')),
                        self::getLocaleTab('de', __('filament.contact.lang_de')),
                        self::getLocaleTab('ar', 'العربية', 'rtl'),
                    ])
                    ->columnSpanFull(),

                Section::make(__('filament.promos.author_section'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('author_name')
                                    ->label(__('filament.promos.author_name'))
                                    ->placeholder('Ex: Fayçal Moussouni')
                                    ->maxLength(255),
                                TextInput::make('author_role')
                                    ->label(__('filament.promos.author_role'))
                                    ->placeholder('Ex: Développeur Fullstack')
                                    ->maxLength(255),
                                FileUpload::make('image_url')
                                    ->label(__('filament.promos.image_label'))
                                    ->image()
                                    ->disk('public')
                                    ->directory('promos')
                                    ->visibility('public'),
                            ]),
                        Placeholder::make('public_url')
                            ->label(__('filament.promos.api_url'))
                            ->content(function (?Promo $record) {
                                if (! $record) {
                                    return __('filament.promos.api_url_info');
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
                            ->columnSpanFull()
                            ->helperText(__('filament.promos.api_url_help')),
                    ]),

                Section::make(__('filament.promos.schedule_section'))
                    ->description(__('filament.promos.schedule_desc'))
                    ->schema([
                        // Planification - Statut & Dates
                        Grid::make(2)
                            ->schema([
                                Select::make('status')
                                    ->label(__('filament.promos.status'))
                                    ->options(PromoStatus::class)
                                    ->required()
                                    ->default(PromoStatus::DRAFT)
                                    ->live()
                                    ->native(false)
                                    ->helperText(__('filament.promos.status_help')),
                                Slider::make('priority')
                                    ->label(__('filament.promos.priority'))
                                    ->required()
                                    ->minValue(1)
                                    ->maxValue(10)
                                    ->fillTrack()
                                    ->step(1)
                                    ->pips(PipsMode::Steps)
                                    ->decimalPlaces(0)
                                    ->default(1)
                                    ->tooltips()
                                    ->helperText(__('filament.promos.priority_help')),
                            ]),
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('starts_at')
                                    ->label(__('filament.promos.start_date'))
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(function (callable $set, callable $get, $livewire) {
                                        self::updateStatusBasedOnDates($set, $get);
                                        $livewire->validateOnly('data.ends_at');
                                    }),
                                DateTimePicker::make('ends_at')
                                    ->label(__('filament.promos.end_date'))
                                    ->native(false)
                                    ->afterOrEqual('starts_at')
                                    ->validationMessages([
                                        'after_or_equal' => __('filament.promos.end_date_validation'),
                                    ])
                                    ->live()
                                    ->afterStateUpdated(function (callable $set, callable $get, $livewire) {
                                        self::updateStatusBasedOnDates($set, $get);
                                        $livewire->validateOnly('data.ends_at');
                                    }),
                            ]),

                        // Mode d'affichage & Limites
                        Grid::make(2)
                            ->schema([
                                Select::make('display_mode')
                                    ->label(__('filament.promos.display_mode'))
                                    ->options([
                                        'fixed_count' => __('filament.promos.display_mode_fixed'),
                                        'unlimited' => __('filament.promos.display_mode_unlimited'),
                                        'once_per_day' => __('filament.promos.display_mode_daily'),
                                        'once_per_week' => __('filament.promos.display_mode_weekly'),
                                    ])
                                    ->default('fixed_count')
                                    ->required()
                                    ->native(false)
                                    ->live()
                                    ->helperText(__('filament.promos.display_mode_help')),
                                Select::make('message_display_mode')
                                    ->label(__('filament.promos.message_display_mode'))
                                    ->options([
                                        'multiline' => __('filament.promos.message_display_multiline'),
                                        'marquee' => __('filament.promos.message_display_marquee'),
                                        'none' => __('filament.promos.message_display_none'),
                                    ])
                                    ->default('multiline')
                                    ->native(false)
                                    ->helperText(__('filament.promos.message_display_help')),
                            ]),
                        TextInput::make('max_impressions')
                            ->label(__('filament.promos.max_impressions'))
                            ->numeric()
                            ->default(9999)
                            ->required()
                            ->visible(fn ($get) => $get('display_mode') === 'fixed_count')
                            ->helperText(__('filament.promos.max_impressions_help')),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('cooldown_seconds')
                                    ->label(__('filament.promos.cooldown_seconds'))
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->helperText(__('filament.promos.cooldown_help')),
                                Select::make('animation_style')
                                    ->label(__('filament.promos.animation_style'))
                                    ->options([
                                        'fade' => __('filament.promos.animation_fade'),
                                        'slide' => __('filament.promos.animation_slide'),
                                        'zoom' => __('filament.promos.animation_zoom'),
                                    ])
                                    ->native(false)
                                    ->helperText(__('filament.promos.animation_help')),
                            ]),

                        // Comportement avancé
                        Grid::make(2)
                            ->schema([
                                TextInput::make('auto_close_timer')
                                    ->label(__('filament.promos.auto_close'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText(__('filament.promos.auto_close_help')),
                                Toggle::make('show_countdown')
                                    ->label(__('filament.promos.show_countdown'))
                                    ->helperText(__('filament.promos.show_countdown_help')),
                            ]),
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

        $startsAt = $startsAtRaw ? Carbon::parse($startsAtRaw) : null;
        $endsAt = $endsAtRaw ? Carbon::parse($endsAtRaw) : null;
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

                // Ersetzt den RichEditor durch ein einfaches Textarea für Klartext (Anforderung: Nur Klartext, kein HTML)
                Textarea::make("content.{$locale}")
                    ->label("Inhalt ({$label})")
                    ->required(fn () => $locale === 'fr')
                    ->rows(5)
                    ->helperText('Nur Klartext (kein HTML)')
                    ->columnSpanFull()
                    ->extraAttributes(['dir' => $direction]),

                Grid::make(2)
                    ->schema([
                        TextInput::make("cta_text.{$locale}")
                            ->label(__('filament.promos.button_text_label')." ({$label})")
                            ->maxLength(255)
                            ->extraInputAttributes(['dir' => $direction]),

                        TextInput::make('cta_url')
                            ->label(__('filament.promos.button_url_label'))
                            ->url()
                            ->maxLength(255)
                            ->helperText(__('filament.promos.button_url_help')),
                    ]),
            ]);
    }
}
