<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\Tables;

use Nafiswatsiq\SubbasePayment\Models\PaymentWebhookLog;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentWebhookLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_id')
                    ->label(__('subbase-payment::subbase-payment/webhook.event_id'))
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),
                TextColumn::make('event_type')
                    ->label(__('subbase-payment::subbase-payment/webhook.event_type'))
                    ->searchable()
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('gateway_driver')
                    ->label(__('subbase-payment::subbase-payment/webhook.gateway_driver'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('subbase-payment::subbase-payment/webhook.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',
                        'received' => 'info',
                        'duplicate' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('subbase-payment::subbase-payment/webhook.created_at'))
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('gateway_driver')
                    ->label(__('subbase-payment::subbase-payment/webhook.gateway_driver'))
                    ->options(fn () => PaymentWebhookLog::query()->distinct()->pluck('gateway_driver', 'gateway_driver')->all()),
                SelectFilter::make('status')
                    ->label(__('subbase-payment::subbase-payment/webhook.status'))
                    ->options([
                        'received' => 'Received',
                        'verified' => 'Verified',
                        'duplicate' => 'Duplicate',
                        'failed' => 'Failed',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }
}
