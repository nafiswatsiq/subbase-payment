<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Nafiswatsiq\SubbasePayment\Models\SubscriptionPayment;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPaymentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SubscriptionPayment');
    }

    public function view(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return $authUser->can('View:SubscriptionPayment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SubscriptionPayment');
    }

    public function update(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return $authUser->can('Update:SubscriptionPayment');
    }

    public function delete(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return $authUser->can('Delete:SubscriptionPayment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SubscriptionPayment');
    }

    public function restore(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return $authUser->can('Restore:SubscriptionPayment');
    }

    public function forceDelete(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return $authUser->can('ForceDelete:SubscriptionPayment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SubscriptionPayment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SubscriptionPayment');
    }

    public function replicate(AuthUser $authUser, SubscriptionPayment $subscriptionPayment): bool
    {
        return $authUser->can('Replicate:SubscriptionPayment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SubscriptionPayment');
    }

}