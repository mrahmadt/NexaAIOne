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
        Schema::create('debugs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_id');
            $table->foreign('api_id')->references('id')->on('apis')->onDelete('cascade');
            $table->string('session')->default('global');
            $table->json('input')->nullable();
            $table->json('output')->nullable();
            $table->json('backtrace')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debugs');
    }
};