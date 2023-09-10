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
        Schema::create('api_end_point_tool', function (Blueprint $table) {
            $table->unsignedBigInteger('api_end_point_id');
            $table->unsignedBigInteger('tool_id');
            $table->primary(['api_end_point_id', 'tool_id']);  // Composite primary key

            $table->foreign('api_end_point_id')
                ->references('id')
                ->on('api_end_points')
                ->onDelete('cascade');  // Setup cascading on delete

            $table->foreign('tool_id')
                ->references('id')
                ->on('tools')
                ->onDelete('cascade');  // Setup cascading on delete

            $table->timestamps();  // Optional: if you need to know when the relation was established
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_end_point_tool');
    }
};
