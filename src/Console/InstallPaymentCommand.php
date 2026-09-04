<?php

namespace Nafiswatsiq\SubbasePayment\Console;

use Illuminate\Console\Command;

class InstallPaymentCommand extends Command
{
    protected $signature = 'subbase-payment:install {--driver= : Payment driver to configure}';

    protected $description = 'Install and configure Subbase Payment';

    /**
     * Environment variable stubs per driver.
     * Keys already present in .env with a non-placeholder value are kept.
     */
    protected array $driverEnvStubs = [
        'paypal' => [
            'PAYPAL_CLIENT_ID' => 'your-client-id',
            'PAYPAL_SECRET' => 'your-secret',
            'PAYPAL_BASE_URL' => 'https://api-m.sandbox.paypal.com',
            'PAYPAL_WEBHOOK_ID' => 'your-webhook-id',
        ],
    ];

    protected array $availableDrivers = ['paypal'];

    public function handle(): int
    {
        $driver = $this->option('driver');

        if (! $driver && $this->input->isInteractive()) {
            $driver = $this->choice('Payment gateway', $this->availableDrivers, 0);
        }

        if (! in_array($driver, $this->availableDrivers, true)) {
            $list = implode(', ', $this->availableDrivers);
            $this->error($driver ? "Unsupported payment driver. Available drivers: {$list}." : "A payment driver is required. Use --driver=paypal.");

            return self::FAILURE;
        }

        if (! $this->updateEnvironment('SUBBASE_PAYMENT_DRIVER', $driver)) {
            return self::FAILURE;
        }

        $this->writeDriverEnvStubs($driver);

        $this->info("Subbase Payment configured with the {$driver} driver.");

        return self::SUCCESS;
    }

    private function writeDriverEnvStubs(string $driver): void
    {
        $stubs = $this->driverEnvStubs[$driver] ?? [];

        foreach ($stubs as $key => $placeholder) {
            $this->setEnvIfMissing($key, $placeholder);
        }
    }

    /**
     * Write an env key only when it is absent or still holds a placeholder value.
     */
    private function setEnvIfMissing(string $key, string $placeholder): void
    {
        $path = base_path('.env');
        $contents = file_exists($path) ? file_get_contents($path) : '';

        if (preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $contents, $matches)) {
            $existing = trim($matches[1], " \t\"");

            // Already configured with a real value — skip.
            if ($existing !== '' && $existing !== $placeholder) {
                return;
            }
        }

        // Write placeholder (or overwrite empty / identical placeholder).
        $line = $key . '=' . $placeholder;

        if (preg_match('/^' . preg_quote($key, '/') . '=.*/m', $contents)) {
            $contents = preg_replace('/^' . preg_quote($key, '/') . '=.*/m', $line, $contents);
        } else {
            $contents = rtrim($contents) . PHP_EOL . $line . PHP_EOL;
        }

        file_put_contents($path, $contents);
    }

    private function updateEnvironment(string $key, string $value): bool
    {
        $path = base_path('.env');
        $contents = file_exists($path) ? file_get_contents($path) : '';
        $line = $key.'='.$value;
        $existingValue = null;

        if (preg_match('/^'.preg_quote($key, '/').'=(.*)$/m', $contents, $matches)) {
            $existingValue = trim($matches[1], " \t\"");
        }

        if ($existingValue !== null && $existingValue !== '' && $existingValue !== $value) {
            if (! $this->input->isInteractive() || ! $this->confirm("Replace existing {$key}?", false)) {
                $this->warn("Keeping existing {$key}; no changes were made.");

                return false;
            }
        }

        if (preg_match('/^'.preg_quote($key, '/').'=.*/m', $contents)) {
            $contents = preg_replace('/^'.preg_quote($key, '/').'=.*/m', $line, $contents);
        } else {
            $contents = rtrim($contents).PHP_EOL.$line.PHP_EOL;
        }

        file_put_contents($path, $contents);

        return true;
    }
}