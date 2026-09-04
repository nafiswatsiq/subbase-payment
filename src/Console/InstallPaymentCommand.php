<?php

namespace Nafiswatsiq\SubbasePayment\Console;

use Illuminate\Console\Command;

class InstallPaymentCommand extends Command
{
    protected $signature = 'subbase-payment:install {--driver= : Payment driver to configure}';

    protected $description = 'Install and configure Subbase Payment';

    public function handle(): int
    {
        $driver = $this->option('driver');

        if (! $driver && $this->input->isInteractive()) {
            $driver = $this->choice('Payment gateway', ['paypal'], 0);
        }

        if ($driver !== 'paypal') {
            $this->error($driver ? 'Unsupported payment driver. Available drivers: paypal.' : 'A payment driver is required. Use --driver=paypal.');

            return self::FAILURE;
        }

        if (! $this->updateEnvironment('SUBBASE_PAYMENT_DRIVER', $driver)) {
            return self::FAILURE;
        }

        $this->info('Subbase Payment configured with the paypal driver.');

        return self::SUCCESS;
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