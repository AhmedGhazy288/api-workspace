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
    public function up(): void
    {
        Schema::table('reservations', static function (Blueprint $table) {
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')
                ->cascadeOnUpdate()->nullOnDelete();
            $table->json('promo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('reservations', static function (Blueprint $table) {
            $table->dropColumn('promo_code_id');
            $table->dropColumn('promo');
        });
    }
};
