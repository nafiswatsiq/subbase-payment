<?php

namespace Nafiswatsiq\SubbasePayment\Console;

use Illuminate\Console\Command;

class ResetPaymentCommand extends Command
{
    protected $signature = 'subbase-payment:reset {--driver= : Switch to new payment driver after reset} {--force : Force reset without confirmation prompt}';

    protected $description = 'Reset Subbase Payment driver configuration and environment keys';

    protected array $driverEnvKeys = [
        'paypal' => [
            'PAYPAL_CLIENT_ID',
            'PAYPAL_SECRET',
            'PAYPAL_BASE_URL',
            'PAYPAL_WEBHOOK_ID',
        ],
    ];

    public function handle(): int
    {
        if (! $this->option('force') && $this->input->isInteractive()) {
            if (! $this->confirm('This will remove SUBBASE_PAYMENT_DRIVER and driver env configurations. Continue?', false)) {
                $this->info('Reset canceled.');

                return self::SUCCESS;
            }
        }

        $this->removeEnvKeys();
        $this->info('Subbase Payment configuration reset successfully.');

        $newDriver = $this->option('driver');
        if ($newDriver) {
            return $this->call('subbase-payment:install', ['--driver' => $newDriver]);
        }

        return self::SUCCESS;
    }

    private function removeEnvKeys(): void
    {
        $path = base_path('.env');
        if (! file_exists($path)) {
            return;
        }

        $contents = file_get_contents($path);
        $keysToRemove = ['SUBBASE_PAYMENT_DRIVER'];

        foreach ($this->driverEnvKeys as $keys) {
            $keysToRemove = array_merge($keysToRemove, $keys);
        }

        foreach (array_unique($keysToRemove) as $key) {
            $contents = preg_replace('/^' . preg_quote($key, '/') . '=.*\r?\n?/m', '', $contents);
        }

        file_put_contents($path, $contents);
    }
}
