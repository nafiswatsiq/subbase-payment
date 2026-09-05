<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments;

use Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments\Pages\ListSubscriptionPayments;
use Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments\Pages\ViewSubscriptionPayment;
use Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments\Schemas\SubscriptionPaymentForm;
use Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments\Tables\SubscriptionPaymentsTable;
use Nafiswatsiq\SubbasePayment\Models\SubscriptionPayment;
use Nafiswatsiq\SubbasePayment\Support\SubbasePaymentPermission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPaymentResource extends Resource
{
    protected static ?string $model = SubscriptionPayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'viewAny', static::getModel());
    }

    public static function canViewAny(): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'viewAny', static::getModel());
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
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'delete', static::getModel());
    }

    public static function canDeleteAny(): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'deleteAny', static::getModel());
    }

    public static function canView(Model $record): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'view', static::getModel());
    }

    public static function getNavigationLabel(): string
    {
        return __('subbase-payment::subbase-payment/payment.navigation_label');
    }

    public static function getNavigationGroup(): string
    {
        return __('subbase-payment::subbase-payment/payment.navigation_group');
    }

    public static function form(Schema $schema): Schema
    {
        return SubscriptionPaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubscriptionPaymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptionPayments::route('/'),
            'view' => ViewSubscriptionPayment::route('/{record}'),
        ];
    }
}
