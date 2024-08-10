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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string("scan_code")->nullable()->unique();
            $table->string("name");
            $table->string("photo")->default('');
            $table->unsignedDouble("cost_price");
            $table->unsignedDouble("retail_price");
            $table->unsignedBigInteger("stock");
            $table->enum("status", ["1", "0"])->default("1");
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
        Schema::dropIfExists('products');
    }
};
