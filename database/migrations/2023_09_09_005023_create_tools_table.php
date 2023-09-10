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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->string('description', 255)->nullable();
            $table->boolean('isActive')->default(true);
            $table->string('aiName', 100);
            $table->string('aiDescription', 255)->nullable();
            $table->json('aiParameters');
            
            $table->json('config')->nullable();
            $table->unsignedTinyInteger('type_id')->default(0);
            $table->string('className', 50)->nullable();
            $table->string('apiURL', 200)->nullable();
            $table->unsignedTinyInteger('apiMethod_id')->default(0);
            $table->json('apiHeader')->nullable();
            $table->unsignedTinyInteger('apiBodyMethod_id')->default(0);
            $table->json('apiBody')->nullable();
            $table->boolean('exposeAsAPI')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
