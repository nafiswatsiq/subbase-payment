<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments\Tables;

use Nafiswatsiq\SubbasePayment\Models\SubscriptionPayment;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubscriptionPaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gateway_transaction_id')
                    ->label(__('subbase-payment::subbase-payment/payment.transaction_id'))
                    ->searchable()
                    ->copyable()
                    ->placeholder('—'),
                TextColumn::make('customer_name')
                    ->label(__('subbase-payment::subbase-payment/payment.customer_name'))
                    ->searchable()
                    ->description(fn (SubscriptionPayment $record) => $record->customer_email),
                TextColumn::make('gateway_driver')
                    ->label(__('subbase-payment::subbase-payment/payment.gateway_driver'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('amount')
                    ->label(__('subbase-payment::subbase-payment/payment.amount'))
                    ->formatStateUsing(fn (SubscriptionPayment $record) => number_format((float) $record->amount, 2) . ' ' . $record->currency)
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label(__('subbase-payment::subbase-payment/payment.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'completed' => 'info',
                        'approved' => 'info',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'canceled' => 'gray',
                        default => 'info',
                    })
                    ->searchable(),
                TextColumn::make('webhook_event_id')
                    ->label(__('subbase-payment::subbase-payment/payment.webhook_event_id'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('verified_at')
                    ->label(__('subbase-payment::subbase-payment/payment.verified_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label(__('subbase-payment::subbase-payment/payment.created_at'))
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('gateway_driver')
                    ->label(__('subbase-payment::subbase-payment/payment.gateway_driver'))
                    ->options(fn () => SubscriptionPayment::query()->distinct()->pluck('gateway_driver', 'gateway_driver')->all()),
                SelectFilter::make('payment_status')
                    ->label(__('subbase-payment::subbase-payment/payment.status'))
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'completed' => 'Completed',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'canceled' => 'Canceled',
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
