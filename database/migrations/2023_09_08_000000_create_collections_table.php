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
            $table->string('name', 150);
            $table->string('description', 255)->nullable();
            $table->string('authToken', 100);
            $table->string('collection_type', 100)->nullable(); // null , api, app, memory, ....etc
            $table->text('context_prompt')->nullable();
            $table->integer('defaultTotalReturnDocuments')->default(3);
            $table->unsignedBigInteger('app_id')->nullable();
            $table->unsignedBigInteger('loader_id')->nullable();
            $table->unsignedBigInteger('splitter_id')->nullable();;
            $table->unsignedBigInteger('embedder_id')->nullable();;
            $table->timestamps();

            // Foreign keys
            $table->foreign('loader_id')->references('id')->on('loaders');
            $table->foreign('splitter_id')->references('id')->on('splitters');
            $table->foreign('embedder_id')->references('id')->on('embedders');
            $table->foreign('app_id')->references('id')->on('apps');
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
