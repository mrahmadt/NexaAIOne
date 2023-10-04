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
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('description', 255)->nullable();
            $table->string('authToken', 100);
            $table->string('context_prompt',255)->nullable();
            $table->integer('defaultTotalReturnDocuments')->default(3);
            $table->unsignedBigInteger('loader_id')->nullable();
            $table->unsignedBigInteger('splitter_id')->nullable();;
            $table->unsignedBigInteger('embedder_id')->nullable();;
            $table->timestamps();

            // Foreign keys
            $table->foreign('loader_id')->references('id')->on('loaders');
            $table->foreign('splitter_id')->references('id')->on('splitters');
            $table->foreign('embedder_id')->references('id')->on('embedders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
