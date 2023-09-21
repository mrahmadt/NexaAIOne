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
        Schema::create('apis', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('description', 255)->nullable();
            $table->string('endpoint', 100);
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('collection_id')->nullable();
            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('collection_id')->references('id')->on('collections');
            $table->boolean('enableUsage')->default(true);
            $table->boolean('isActive')->default(true);
            $table->json('options');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apis');
    }
};
