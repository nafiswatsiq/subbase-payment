<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('subbase-payment::subbase-payment/payment.basic_information'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('gateway_transaction_id')
                            ->label(__('subbase-payment::subbase-payment/payment.transaction_id')),
                        TextInput::make('subscription_id')
                            ->label(__('subbase-payment::subbase-payment/payment.subscription_id')),
                        TextInput::make('gateway_driver')
                            ->label(__('subbase-payment::subbase-payment/payment.gateway_driver'))
                            ->required(),
                        Select::make('payment_status')
                            ->label(__('subbase-payment::subbase-payment/payment.status'))
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'canceled' => 'Canceled',
                            ])
                            ->required(),
                        TextInput::make('customer_name')
                            ->label(__('subbase-payment::subbase-payment/payment.customer_name')),
                        TextInput::make('customer_email')
                            ->label(__('subbase-payment::subbase-payment/payment.customer_email'))
                            ->email(),
                        TextInput::make('amount')
                            ->label(__('subbase-payment::subbase-payment/payment.amount'))
                            ->numeric(),
                        TextInput::make('currency')
                            ->label(__('subbase-payment::subbase-payment/payment.currency')),
                        TextInput::make('webhook_event_id')
                            ->label(__('subbase-payment::subbase-payment/payment.webhook_event_id')),
                        DateTimePicker::make('verified_at')
                            ->label(__('subbase-payment::subbase-payment/payment.verified_at')),
                    ]),
                Section::make(__('subbase-payment::subbase-payment/payment.metadata'))
                    ->columnSpanFull()
                    ->schema([
                        KeyValue::make('metadata')
                            ->label(__('subbase-payment::subbase-payment/payment.metadata')),
                    ]),
            ]);
    }
}
