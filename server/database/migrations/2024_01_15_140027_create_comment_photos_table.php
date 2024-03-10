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
        Schema::create('comment_photos', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->unsignedInteger('photo_id'); 
            $table->unsignedInteger('user_id');  
            $table->text('comment');
            $table->unsignedInteger('parent_id')->nullable(); 
            $table->timestamp('created_at')->nullable();
            $table->softDeletes();

            $table->foreign('photo_id')->references('id')->on('photos');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('parent_id')->references('id')->on('comment_photos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_photos');
    }
};
