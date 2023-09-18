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
            $table->unsignedBigInteger('api_id');
            // Uncomment the next line if you wish to add a foreign key constraint.
            // $table->foreign('api_id')->references('id')->on('apis');
            $table->string('sessionHash',40)->default('global'); //MD5 hashed string
            $table->json('messages')->nullable(); //JSON array of messages
            $table->json('messagesMeta')->nullable(); //JSON array of messages meta
            $table->timestamps();

            $table->unique(['api_id', 'sessionHash']);
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

