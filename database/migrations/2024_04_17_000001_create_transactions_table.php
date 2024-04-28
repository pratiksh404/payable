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
        Schema::create(config('payable.table_prefix', 'payable_').'transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('payment_method');
            $table->double('amount');
            $table->boolean('success')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('payable.table_prefix', 'payable_').'transactions');
    }
};
