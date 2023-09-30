<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

//https://github.com/pgvector/pgvector-php

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->vector('embeds', 1536)->nullable(); // Dimensionality; 1536 for OpenAI's ada-002
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('content_tokens')->nullable();
            $table->unsignedBigInteger('collection_id');
            $table->timestamps();
            $table->foreign('collection_id')->references('id')->on('collections');
        });

        // https://github.com/pgvector/pgvector#query-options
        DB::statement('CREATE INDEX ON documents USING hnsw (embeds vector_cosine_ops)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
