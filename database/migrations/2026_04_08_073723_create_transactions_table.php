<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->uuid('transaction_id')->unique();

            $table->string('md5')->nullable();

            $table->decimal('amount', 12, 2);
            $table->string('currency', 10);

            $table->string('status')->default('pending');

            // Store full webhook payload
            $table->json('payload')->nullable();

            $table->string('bakong_tx_id')->nullable();

            $table->timestamps();

            $table->index('transaction_id');
            $table->index('md5');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
