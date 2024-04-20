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
        Schema::create(config('payable.table_prefix', 'payable_') . 'fiscals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('year');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('payable.table_prefix', 'payable_') . 'fiscals');
    }
};
