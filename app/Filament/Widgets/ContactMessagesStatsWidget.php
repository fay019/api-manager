<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContactMessagesStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('filament.contact.stats_total'), ContactMessage::count())
                ->description(__('filament.contact.stats_total_desc'))
                ->icon('heroicon-o-envelope')
                ->color('primary'),

            Stat::make(__('filament.contact.stats_new'), ContactMessage::where('status', 'new')->count())
                ->description(__('filament.contact.stats_new_desc'))
                ->icon('heroicon-o-bell')
                ->color('warning'),

            Stat::make(__('filament.contact.stats_read'), ContactMessage::where('status', 'read')->count())
                ->description(__('filament.contact.stats_read_desc'))
                ->icon('heroicon-o-eye')
                ->color('info'),

            Stat::make(__('filament.contact.stats_replied'), ContactMessage::whereNotNull('replied_at')->count())
                ->description(__('filament.contact.stats_replied_desc'))
                ->icon('heroicon-o-arrow-path')
                ->color('success'),

            Stat::make(__('filament.contact.stats_spam'), ContactMessage::where('status', 'spam')->count())
                ->description(__('filament.contact.stats_spam_desc'))
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
