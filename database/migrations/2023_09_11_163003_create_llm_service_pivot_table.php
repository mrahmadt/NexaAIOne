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
        Schema::create('llm_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('llm_id');
            $table->unsignedBigInteger('service_id');
            $table->timestamps();
            $table->foreign('llm_id')->references('id')->on('llms')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llm_service');
    }
};
