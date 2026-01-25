<?php

namespace App\Filament\Resources\Promos\Schemas;

use App\Enums\PromoStatus;
use App\Models\Promo;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class PromoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations de la Promo')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Titre')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($set, $state, $context) {
                                        if ($context === 'create') {
                                            $set('slug', \Illuminate\Support\Str::slug($state));
                                        }
                                    }),
                                TextInput::make('slug')
                                    ->label('Slug / Public Path')
                                    ->unique(Promo::class, 'slug', ignoreRecord: true)
                                    ->rules(['alpha_dash'])
                                    ->maxLength(255)
                                    ->helperText('Utilisé pour l\'URL publique. Ex: banner-hiver. Laissez vide pour utiliser l\'ID.'),
                            ]),
                        Textarea::make('content')
                            ->label('Contenu')
                            ->required()
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                TextInput::make('cta_text')
                                    ->label('Texte du bouton (CTA)')
                                    ->maxLength(255),
                                TextInput::make('cta_url')
                                    ->label('Lien du bouton (CTA)')
                                    ->url()
                                    ->maxLength(255),
                            ]),
                    ]),

                Section::make('Lien public / Endpoint')
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
}
