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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('trans_id')->nullable();
            $table->string('trans_address')->nullable();
            $table->string('trans_amount')->nullable();
            $table->string('trans_fee')->nullable();
            $table->string('trans_status')->nullable();
            $table->string('trans_loaded')->nullable();
            $table->string('trans_from')->nullable();
            $table->string('trans_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('trans_id');
            $table->dropColumn('trans_address');
            $table->dropColumn('trans_amount');
            $table->dropColumn('trans_fee');
            $table->dropColumn('trans_status');
            $table->dropColumn('trans_loaded');
            $table->dropColumn('trans_from');
            $table->dropColumn('trans_to');
        });
    }
};
