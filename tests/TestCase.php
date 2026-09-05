<?php

namespace Nafiswatsiq\SubbasePayment\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Nafiswatsiq\SubbasePayment\SubbasePaymentServiceProvider;
use Nafiswatsiq\Subbase\SubbaseServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            SubbaseServiceProvider::class,
            SubbasePaymentServiceProvider::class,
        ];
    }
}
