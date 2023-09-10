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
        Schema::create('llm_api_end_point', function (Blueprint $table) {
            $table->unsignedBigInteger('api_end_point_id');
            $table->unsignedBigInteger('llm_id');
            $table->primary(['api_end_point_id', 'llm_id']);  // Composite primary key

            $table->foreign('api_end_point_id')
                ->references('id')
                ->on('api_end_points')
                ->onDelete('cascade');  // Setup cascading on delete

            $table->foreign('llm_id')
                ->references('id')
                ->on('llms')
                ->onDelete('cascade');  // Setup cascading on delete

            $table->timestamps();  // Optional: if you need to know when the relation was established
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('llm_api_end_point');
    }
};
