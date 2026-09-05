<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentWebhookLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('subbase-payment::subbase-payment/webhook.basic_information'))
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('gateway_driver')
                            ->label(__('subbase-payment::subbase-payment/webhook.gateway_driver')),
                        TextInput::make('event_id')
                            ->label(__('subbase-payment::subbase-payment/webhook.event_id')),
                        TextInput::make('event_type')
                            ->label(__('subbase-payment::subbase-payment/webhook.event_type')),
                        TextInput::make('status')
                            ->label(__('subbase-payment::subbase-payment/webhook.status')),
                    ]),
                Section::make(__('subbase-payment::subbase-payment/webhook.headers'))
                    ->columnSpanFull()
                    ->schema([
                        KeyValue::make('headers')
                            ->label(__('subbase-payment::subbase-payment/webhook.headers')),
                    ]),
                Section::make(__('subbase-payment::subbase-payment/webhook.payload'))
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('payload')
                            ->label(__('subbase-payment::subbase-payment/webhook.payload'))
                            ->formatStateUsing(fn ($state) => is_array($state) || is_object($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : (string) $state)
                            ->rows(12),
                    ]),
                Section::make(__('subbase-payment::subbase-payment/webhook.error_information'))
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('error_message')
                            ->label(__('subbase-payment::subbase-payment/webhook.error_message'))
                            ->rows(3),
                    ]),
            ]);
    }
}
