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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('description', 255)->nullable();
            $table->string('className', 100);
            $table->string('reference', 150)->nullable();;
            $table->boolean('supportHistory')->default(false);
            $table->boolean('supportCaching')->default(false);
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
