<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs;

use Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\Pages\ListPaymentWebhookLogs;
use Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\Pages\ViewPaymentWebhookLog;
use Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\Schemas\PaymentWebhookLogForm;
use Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\Tables\PaymentWebhookLogsTable;
use Nafiswatsiq\SubbasePayment\Models\PaymentWebhookLog;
use Nafiswatsiq\SubbasePayment\Support\SubbasePaymentPermission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentWebhookLogResource extends Resource
{
    protected static ?string $model = PaymentWebhookLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCommandLine;

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'viewAny', static::getModel());
    }

    public static function canViewAny(): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'viewAny', static::getModel());
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'delete', static::getModel());
    }

    public static function canDeleteAny(): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'deleteAny', static::getModel());
    }

    public static function canView(Model $record): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'view', static::getModel());
    }

    public static function getNavigationLabel(): string
    {
        return __('subbase-payment::subbase-payment/webhook.navigation_label');
    }

    public static function getNavigationGroup(): string
    {
        return __('subbase-payment::subbase-payment/webhook.navigation_group');
    }

    public static function form(Schema $schema): Schema
    {
        return PaymentWebhookLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentWebhookLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentWebhookLogs::route('/'),
            'view' => ViewPaymentWebhookLog::route('/{record}'),
        ];
    }
}
