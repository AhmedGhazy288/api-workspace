<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained('cards')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('client_id')->nullable()->constrained('clients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime("starts_at");
            $table->dateTime("ends_at")->nullable();
            $table->unsignedDouble("reservation_total", 8, 2)->default(0);
            $table->unsignedDouble("products_total", 8, 2)->default(0);
            $table->unsignedDouble("total", 8, 2)->default(0);
            $table->unsignedDouble("amount_paid", 8, 2)->default(0);
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
        Schema::dropIfExists('reservations');
    }
};
