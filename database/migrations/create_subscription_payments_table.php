<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('subbase-payment.tables.subscription_payments', 'subscription_payments');

        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function (Blueprint $table): void {
            $table->id();
            $table->string('subscription_id')->nullable()->index();
            $table->string('gateway_driver')->index();
            $table->string('gateway_transaction_id')->nullable()->unique();
            $table->string('payment_status')->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('amount', 18, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('webhook_event_id')->nullable()->unique();
            $table->timestamp('verified_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tableName = config('subbase-payment.tables.subscription_payments', 'subscription_payments');

        Schema::dropIfExists($tableName);
    }
};