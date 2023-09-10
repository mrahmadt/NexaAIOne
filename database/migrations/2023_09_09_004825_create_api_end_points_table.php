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
            $table->string('apiName', 50)->unique();
            $table->unsignedBigInteger('ai_end_points_id');
            $table->foreign('ai_end_points_id')->references('id')->on('ai_end_points');
            $table->boolean('enableUsage')->default(true);
            $table->boolean('enableHistory')->default(false);
            $table->unsignedTinyInteger('historyMethod_id')->default(0);
            $table->json('historyOptions')->nullable();
            $table->json('requestSchema');
            /*
            name
            description
            type
            required
            default
            value or parameter

            */
            $table->json('toolsConfig')->nullable();
            $table->boolean('enableCaching')->default(false);
            $table->unsignedInteger('cachingPeriod')->default(1440);
            $table->boolean('isActive')->default(true);
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
