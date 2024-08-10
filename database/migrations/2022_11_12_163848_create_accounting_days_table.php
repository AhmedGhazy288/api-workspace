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

        Schema::create('accounting_days', function (Blueprint $table) {
            $table->id();
            $table->date("day");
            $table->double('reservations_count');
            $table->double('reservations_total');
            $table->double('products_total');
            $table->double('final_total');
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
        Schema::dropIfExists('accounting_days');
    }
};
