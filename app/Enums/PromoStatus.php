<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PromoStatus: string implements HasColor, HasIcon, HasLabel
{
    case DRAFT = 'draft';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'Brouillon',
            self::SCHEDULED => 'Programmé',
            self::PUBLISHED => 'Publié',
            self::ARCHIVED => 'Archivé',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SCHEDULED => 'info',
            self::PUBLISHED => 'success',
            self::ARCHIVED => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-m-pencil-square',
            self::SCHEDULED => 'heroicon-m-clock',
            self::PUBLISHED => 'heroicon-m-check-circle',
            self::ARCHIVED => 'heroicon-m-archive-box',
        };
    }
}
