<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('subbase.tables.subscriptions', 'subscriptions');

        if (! Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table): void {
            if (! Schema::hasColumn($table->getTable(), 'gateway_driver')) {
                $table->string('gateway_driver')->nullable()->index();
            }

            if (! Schema::hasColumn($table->getTable(), 'gateway_transaction_id')) {
                $table->string('gateway_transaction_id')->nullable()->index();
            }

            if (! Schema::hasColumn($table->getTable(), 'payment_status')) {
                $table->string('payment_status')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        $tableName = config('subbase.tables.subscriptions', 'subscriptions');

        if (! Schema::hasTable($tableName)) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table): void {
            $columns = collect([
                'gateway_driver',
                'gateway_transaction_id',
                'payment_status',
            ])->filter(fn (string $column): bool => Schema::hasColumn($table->getTable(), $column));

            if ($columns->isNotEmpty()) {
                $table->dropColumn($columns->all());
            }
        });
    }
};
