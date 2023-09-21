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
        Schema::create('chunks', function (Blueprint $table) {
            $table->id();
            $table->json('meta')->nullable();
            // $table->json('embeds');
            $table->vector('embeds', 1536); // Dimensionality; 1536 for OpenAI's ada-002
            $table->unsignedBigInteger('collection_id');
            $table->unsignedBigInteger('document_id');
            $table->timestamps();
            $table->foreign('collection_id')->references('id')->on('collections');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');;
        });
        // This is a Postgres-specific index that allows us to do fast nearest-neighbor searches
        // when there are a lot of high-dimensional embeddings in the database.
        DB::statement('CREATE INDEX my_index ON chunks USING ivfflat (embeds vector_l2_ops) WITH (lists = 100)');
        // or
        // DB::statement('CREATE INDEX my_index ON chunks USING hnsw (embeds vector_l2_ops)');
        //Use vector_ip_ops for inner product and vector_cosine_ops for cosine distance
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chunks');
    }
};