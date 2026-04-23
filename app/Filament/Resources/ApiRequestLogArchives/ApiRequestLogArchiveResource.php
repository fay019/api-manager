<?php

namespace App\Filament\Resources\ApiRequestLogArchives;

use App\Filament\Resources\ApiRequestLogArchives\Pages\ListApiRequestLogArchives;
use App\Models\ApiRequestLogArchive;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('created_at')
                    ->label(__('filament.log.timestamp'))
                    ->disabled(),

                TextInput::make('method')
                    ->label(__('filament.log.method'))
                    ->disabled(),

                TextInput::make('path')
                    ->label(__('filament.log.endpoint'))
                    ->disabled(),

                TextInput::make('status_code')
                    ->label(__('filament.log.status'))
                    ->disabled(),

                TextInput::make('duration_ms')
                    ->label(__('filament.log.duration'))
                    ->disabled(),

                TextInput::make('ip')
                    ->label(__('filament.log.ip'))
                    ->disabled(),

                TextInput::make('hostname')
                    ->label(__('filament.log.hostname'))
                    ->disabled(),

                TextInput::make('domain')
                    ->label(__('filament.log.domain'))
                    ->disabled(),

                TextInput::make('site_name')
                    ->label(__('filament.log.site_name'))
                    ->disabled(),

                TextInput::make('page_path')
                    ->label(__('filament.log.page_path'))
                    ->disabled(),

                TextInput::make('full_url')
                    ->label(__('filament.log.full_url'))
                    ->disabled(),

                TextInput::make('client_request_time')
                    ->label(__('filament.log.client_time'))
                    ->disabled(),

                TextInput::make('client_user_agent')
                    ->label(__('filament.log.browser'))
                    ->disabled(),

                TextInput::make('user_agent')
                    ->label(__('filament.log.user_agent'))
                    ->disabled(),

                TextInput::make('origin')
                    ->label(__('filament.log.origin'))
                    ->disabled()
                    ->placeholder(__('filament.log.not_provided') ?? 'Not provided'),

                TextInput::make('referer')
                    ->label(__('filament.log.referer'))
                    ->disabled()
                    ->placeholder(__('filament.log.not_provided') ?? 'Not provided'),

                TextInput::make('apiClient.name')
                    ->label(__('filament.log.api_client'))
                    ->disabled()
                    ->placeholder(__('filament.log.public_api') ?? 'Public API'),

                TextInput::make('apiKey.name')
                    ->label(__('filament.log.api_key'))
                    ->disabled()
                    ->placeholder(__('filament.log.not_used') ?? 'Not used'),
            ]);
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
            ->actions([
                ViewAction::make()->iconButton(),
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
