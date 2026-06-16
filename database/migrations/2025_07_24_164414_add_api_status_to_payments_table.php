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
            $table->string('api_trans_id')->nullable()->after('trans_to');
            $table->string('api_status')->nullable()->after('api_trans_id');
            $table->text('api_response')->nullable()->after('api_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['api_trans_id', 'api_status', 'api_response']);
        });
    }
};
