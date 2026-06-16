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
        Schema::table('card_activations', function (Blueprint $table) {
            $table->string('card_type')->after('user_id')->nullable();
            $table->bigInteger('card_id')->after('user_id')->nullable();
            $table->bigInteger('card_holder_id')->after('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('card_activations', function (Blueprint $table) {
            $table->dropColumn('card_type');
            $table->dropColumn('card_id');
            $table->dropColumn('card_holder_id');
        });
    }
};
