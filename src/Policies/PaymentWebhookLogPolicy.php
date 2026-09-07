<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Nafiswatsiq\SubbasePayment\Models\PaymentWebhookLog;
use Nafiswatsiq\SubbasePayment\Support\SubbasePaymentPermission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentWebhookLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'viewAny', PaymentWebhookLog::class);
    }

    public function view(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'view', PaymentWebhookLog::class);
    }

    public function create(AuthUser $authUser): bool
    {
        return false;
    }

    public function update(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return false;
    }

    public function delete(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'delete', PaymentWebhookLog::class);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'deleteAny', PaymentWebhookLog::class);
    }

    public function restore(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'restore', PaymentWebhookLog::class);
    }

    public function forceDelete(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'forceDelete', PaymentWebhookLog::class);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'forceDeleteAny', PaymentWebhookLog::class);
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.payment_webhook_log'), 'restoreAny', PaymentWebhookLog::class);
    }

    public function replicate(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
    }
}