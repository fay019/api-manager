<?php

namespace App\Filament\Resources\ApiRequestLogArchives;

use App\Filament\Resources\ApiRequestLogArchives\Pages\ListApiRequestLogArchives;
use App\Models\ApiRequestLogArchive;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ApiRequestLogArchiveResource extends Resource
{
    protected static ?string $model = ApiRequestLogArchive::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|UnitEnum|null $navigationGroup = 'API Management';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.logs_archive') ?? 'Archived Logs';
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.log.archive_plural') ?? 'Archived Logs';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('filament.log.table_time'))
                    ->dateTime('M d H:i:s')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('method')
                    ->label(__('filament.log.table_method'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'GET' => 'info',
                        'POST' => 'success',
                        'PUT' => 'warning',
                        'DELETE' => 'danger',
                        'PATCH' => 'secondary',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('path')
                    ->label(__('filament.log.table_endpoint'))
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('status_code')
                    ->label(__('filament.log.table_status'))
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 300 && $state < 400 => 'warning',
                        $state >= 400 => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('apiClient.name')
                    ->label(__('filament.log.table_client'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('(public)'),

                TextColumn::make('domain')
                    ->label(__('filament.log.table_domain'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->limit(40),

                TextColumn::make('ip')
                    ->label(__('filament.log.table_source'))
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                TextColumn::make('duration_ms')
                    ->label(__('filament.log.table_duration'))
                    ->suffix(' ms')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('method')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'DELETE' => 'DELETE',
                        'PATCH' => 'PATCH',
                    ]),

                Filter::make('status_code_2xx')
                    ->label(__('filament.log.filter_2xx'))
                    ->query(fn (Builder $query) => $query->whereBetween('status_code', [200, 299])),

                Filter::make('status_code_4xx')
                    ->label(__('filament.log.filter_4xx'))
                    ->query(fn (Builder $query) => $query->whereBetween('status_code', [400, 499])),

                Filter::make('status_code_5xx')
                    ->label(__('filament.log.filter_5xx'))
                    ->query(fn (Builder $query) => $query->whereBetween('status_code', [500, 599])),

                SelectFilter::make('api_client_id')
                    ->relationship('apiClient', 'name')
                    ->label(__('filament.log.filter_client')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiRequestLogArchives::route('/'),
        ];
    }
}
