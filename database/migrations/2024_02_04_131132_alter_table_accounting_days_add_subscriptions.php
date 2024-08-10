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
        Schema::table('accounting_days', function (Blueprint $table) {
            $table->double('subscriptions_total')->default(0);
            $table->double('subscriptions_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounting_days', function (Blueprint $table) {
            $table->dropColumn('subscriptions_total');
            $table->dropColumn('subscriptions_count');
        });
    }
};
