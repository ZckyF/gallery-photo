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
        Schema::create('otp', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('otp')->unique();
            $table->timestamp('expires_at');
            $table->boolean('otp_verified')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
