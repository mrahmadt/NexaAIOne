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
        Schema::create('memories', function (Blueprint $table) {
            $table->id(); // Using the id() method to create an auto-incremented primary key
            $table->unsignedBigInteger('app_id');
            $table->unsignedBigInteger('api_id');
            $table->string('sessionHash',40)->default('global'); //MD5 hashed string
            $table->json('messages')->nullable(); //JSON array of messages
            $table->json('messagesMeta')->nullable(); //JSON array of messages meta
            $table->timestamps();
            $table->unique(['app_id', 'api_id', 'sessionHash']);

            $table->foreign('api_id')->references('id')->on('apis')->onDelete('cascade');
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memories');
    }
};

