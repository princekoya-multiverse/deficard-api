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
        Schema::create('kyc_verifications', function (Blueprint $table) {
            $table->id();
            $table->String('first_name');
            $table->String('last_name');
            $table->String('birthday');
            $table->String('email');
            $table->char('phone', 20);
            $table->String('city');
            $table->String('street_address');
            $table->String('street_address_2');
            $table->String('region_state_province');
            $table->String('zipcode');
            $table->String('country');
            $table->String('file1');
            $table->String('file2');
            $table->char('status', 1)->default(0);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kyc_verifications');
    }
};
