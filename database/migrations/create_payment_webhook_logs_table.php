<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('subbase-payment.tables.payment_webhook_logs', 'payment_webhook_logs');

        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function (Blueprint $table): void {
            $table->id();
            $table->string('gateway_driver')->index();
            $table->string('event_id')->nullable()->index();
            $table->string('event_type')->nullable()->index();
            $table->string('status')->default('received')->index(); // received, verified, failed, duplicate
            $table->json('payload')->nullable();
            $table->json('headers')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tableName = config('subbase-payment.tables.payment_webhook_logs', 'payment_webhook_logs');

        Schema::dropIfExists($tableName);
    }
};
