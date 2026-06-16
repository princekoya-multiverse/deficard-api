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
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->string('file3_lang')->nullable()->after('file3');
            $table->string('file3_type')->nullable()->after('file3_lang');
            $table->string('file3_issued_by')->nullable()->after('file3_type');
            $table->string('file3_issued_date')->nullable()->after('file3_issued_by');
            $table->string('card_holder_id')->nullable()->after('file3_issued_date');
            $table->string('card_holder_file_id')->nullable()->after('card_holder_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kyc_verifications', function (Blueprint $table) {
            $table->dropColumn('card_holder_id');
            $table->dropColumn('card_holder_file_id');
            $table->dropColumn('file3_lang');
            $table->dropColumn('file3_type');
            $table->dropColumn('file3_issued_by');
            $table->dropColumn('file3_issued_date');
        });
    }
};
