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
        Schema::create('ai_end_point_llm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('llm_id');
            $table->unsignedBigInteger('ai_end_point_id');
            $table->timestamps();
            $table->foreign('llm_id')->references('id')->on('llms')->onDelete('cascade');
            $table->foreign('ai_end_point_id')->references('id')->on('ai_end_points')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_end_point_llm');
    }
};
