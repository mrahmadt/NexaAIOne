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
        Schema::create('api_end_points', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('description', 255)->nullable();
            $table->string('apiName', 100);
            $table->unsignedBigInteger('ai_end_points_id');
            $table->foreign('ai_end_points_id')->references('id')->on('ai_end_points');
            $table->boolean('enableUsage')->default(true);
            $table->json('toolsConfig')->nullable();
            $table->boolean('isActive')->default(true);
            $table->json('requestSchema');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_end_points');
    }
};
