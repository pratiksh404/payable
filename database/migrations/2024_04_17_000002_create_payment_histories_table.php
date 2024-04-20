<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('payable.table_prefix', 'payable_') . 'payment_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('payment_id');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->ipAddress('ip_address')->nullable();
            $table->integer('action');
            $table->double('old_amount')->nullable();
            $table->double('changed_amount')->nullable();

            $table->boolean('verified')->default(0);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();

            // Foreign 
            $table->foreign('payment_id')->references('id')->on(config('payable.table_prefix', 'payable_') . 'payments')->onDelete('cascade');
            $table->foreign('verified_by')->references(config('payable.user_table_primary_key','id'))->on(config('payable.user_table', 'users'))->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('payable.table_prefix', 'payable_') . 'payment_histories');
    }
};
