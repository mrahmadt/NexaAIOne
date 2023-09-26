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
        Schema::create('usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_id');
            $table->unsignedBigInteger('api_id');
            $table->unsignedBigInteger('hits')->default(0);
            $table->unsignedBigInteger('promptTokens')->default(0);
            $table->unsignedBigInteger('completionTokens')->default(0);
            $table->unsignedBigInteger('totalTokens')->default(0);
            $table->date('date');
            $table->timestamps();
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            $table->foreign('api_id')->references('id')->on('apis')->onDelete('cascade');
            $table->unique(['app_id', 'api_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usages');
    }
};
