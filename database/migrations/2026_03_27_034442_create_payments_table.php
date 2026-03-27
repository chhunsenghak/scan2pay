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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');

            $table->string('md5')->nullable();
            $table->string('hash')->nullable();
            $table->string('short_hash')->nullable();

            $table->string('transaction_id')->nullable();
            $table->string('status')->default('pending');

            $table->json('raw_response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
