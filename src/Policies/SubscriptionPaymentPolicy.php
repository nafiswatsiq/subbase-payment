<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Nafiswatsiq\SubbasePayment\Models\SubscriptionPayment;
use Nafiswatsiq\SubbasePayment\Support\SubbasePaymentPermission;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPaymentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'viewAny', SubscriptionPayment::class);
    }

    public function view(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'view', SubscriptionPayment::class);
    }

    public function create(AuthUser $authUser): bool
    {
        return false;
    }

    public function update(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return false;
    }

    public function delete(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'delete', SubscriptionPayment::class);
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'deleteAny', SubscriptionPayment::class);
    }

    public function restore(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'restore', SubscriptionPayment::class);
    }

    public function forceDelete(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'forceDelete', SubscriptionPayment::class);
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'forceDeleteAny', SubscriptionPayment::class);
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return SubbasePaymentPermission::allows(config('subbase-payment.permissions.subscription_payment'), 'restoreAny', SubscriptionPayment::class);
    }

    public function replicate(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return false;
    }

    public function reorder(AuthUser $authUser): bool
    {
        return false;
    }
}