<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Pratiksh\Payable\Models\Payment;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('payable.table_prefix', 'payable_').'payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('receipt_no')->unique();
            $table->unsignedBigInteger('fiscal_id');
            $table->double('amount')->default(0);
            $table->json('data')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->boolean('type')->default(Payment::CREDIT);

            // Polymorphism
            $table->unsignedBigInteger('paymentable_id');
            $table->string('paymentable_type');
            $table->timestamps();

            // Foreign
            $table->foreign('fiscal_id')->references('id')->on(config('payable.table_prefix', 'payable_').'fiscals')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on(config('payable.table_prefix', 'payable_').'transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('payable.table_prefix', 'payable_').'payments');
    }
};
