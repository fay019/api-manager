<?php

namespace App\Filament\Resources;

use App\Models\ApiRequestLog;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;

class ApiRequestLogResource extends Resource
{
    protected static ?string $model = ApiRequestLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'API Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $label = 'Request Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M d H:i:s')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('method')
                    ->label('Method')
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
                    ->label('Endpoint')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('status_code')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn(ApiRequestLog $record): int => $record->status_code)
                    ->color(fn (int $state): string => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 300 && $state < 400 => 'warning',
                        $state >= 400 => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('apiClient.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->placeholder('(public)'),

                TextColumn::make('ip')
                    ->label('IP Address')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                TextColumn::make('duration_ms')
                    ->label('Duration')
                    ->suffix(' ms')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('origin')
                    ->label('Origin')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->label('2xx Success')
                    ->query(fn(Builder $query) => $query->whereBetween('status_code', [200, 299])),

                Filter::make('status_code_4xx')
                    ->label('4xx Errors')
                    ->query(fn(Builder $query) => $query->whereBetween('status_code', [400, 499])),

                Filter::make('status_code_5xx')
                    ->label('5xx Errors')
                    ->query(fn(Builder $query) => $query->whereBetween('status_code', [500, 599])),

                SelectFilter::make('api_client_id')
                    ->relationship('apiClient', 'name')
                    ->label('Client'),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ApiRequestLogResource\Pages\ListApiRequestLogs::route('/'),
        ];
    }
}
