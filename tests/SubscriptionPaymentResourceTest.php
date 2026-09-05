<?php

namespace Nafiswatsiq\SubbasePayment\Tests;

use Nafiswatsiq\SubbasePayment\Models\SubscriptionPayment;
use Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments\SubscriptionPaymentResource;
use Nafiswatsiq\SubbasePayment\Support\SubbasePaymentPermission;

class SubscriptionPaymentResourceTest extends TestCase
{
    public function test_resource_can_be_instantiated()
    {
        $this->assertEquals(SubscriptionPayment::class, SubscriptionPaymentResource::getModel());
        $this->assertFalse(SubscriptionPaymentResource::canCreate());
    }

    public function test_permission_fallback_allows_access_without_spatie()
    {
        $this->assertTrue(SubbasePaymentPermission::allows(null, 'viewAny', SubscriptionPayment::class));
    }
}
