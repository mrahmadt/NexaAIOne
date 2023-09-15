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
        Schema::create('app_api_end_point', function (Blueprint $table) {
            $table->unsignedBigInteger('app_id'); // Foreign key for App
            $table->unsignedBigInteger('api_end_point_id'); // Foreign key for APIEndPoint
            $table->timestamps(); // Optional: to track when each relationship was established

            // Foreign key constraints
            $table->foreign('app_id')
                ->references('id')
                ->on('apps')
                ->onDelete('cascade'); // If an app is deleted, remove its relationships

            $table->foreign('api_end_point_id')
                ->references('id')
                ->on('api_end_points') // Assuming the APIEndPoint's table name is 'api_end_points'
                ->onDelete('cascade'); // If an APIEndPoint is deleted, remove its relationships

            // Unique constraint to ensure a combination of app_id and api_end_point_id is only stored once
            $table->unique(['app_id', 'api_end_point_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_api_end_point');
    }
};
