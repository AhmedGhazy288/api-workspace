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
        Schema::create('cards', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_type_id')->constrained('card_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("rfid")->unique()->nullable();
            $table->double("balance")->default(0);
            $table->double("cost_per_hour");
            $table->timestamp("ends_at")->nullable();
            $table->enum("status", ["0", '1'])->default("1");

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
        Schema::dropIfExists('cards');
    }
};
