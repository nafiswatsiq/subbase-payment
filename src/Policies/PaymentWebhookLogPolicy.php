<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Nafiswatsiq\SubbasePayment\Models\PaymentWebhookLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentWebhookLogPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PaymentWebhookLog');
    }

    public function view(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return $authUser->can('View:PaymentWebhookLog');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PaymentWebhookLog');
    }

    public function update(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return $authUser->can('Update:PaymentWebhookLog');
    }

    public function delete(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return $authUser->can('Delete:PaymentWebhookLog');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PaymentWebhookLog');
    }

    public function restore(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return $authUser->can('Restore:PaymentWebhookLog');
    }

    public function forceDelete(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return $authUser->can('ForceDelete:PaymentWebhookLog');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PaymentWebhookLog');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PaymentWebhookLog');
    }

    public function replicate(AuthUser $authUser, PaymentWebhookLog $paymentWebhookLog): bool
    {
        return $authUser->can('Replicate:PaymentWebhookLog');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PaymentWebhookLog');
    }

}