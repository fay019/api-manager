<?php

namespace App\Filament\Resources\Promos\Schemas;

use App\Enums\PromoStatus;
use App\Support\FeatureFlag;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Slider\Enums\PipsMode;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PromoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations de la Promo')
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255),
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
        if (!$startsAtRaw && !$endsAtRaw) {
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
