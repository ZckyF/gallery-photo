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
        Schema::create('users', function (Blueprint $table) {
            $table->integerIncrements('id'); 
            $table->string('username', 30)->unique();
            $table->string('password', 255);
            $table->string('email', 100)->unique();
            $table->string('full_name', 50);
            $table->text('address');
            $table->text('bio')->nullable();
            $table->boolean('is_actived')->default(0);
            $table->string('avatar_name', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
