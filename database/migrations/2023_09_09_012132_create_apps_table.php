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
        Schema::create('apps', function (Blueprint $table) {
            $table->id(); // Primary unsigned int ID
            $table->string('name', 40); // varchar 40 for name
            $table->string('description', 255)->nullable(); // varchar 255 for description with default NULL
            $table->string('owner', 50); // varchar 50 for owner
            $table->string('email', 100); // varchar 100 for email
            $table->boolean('isActive')->default(true); // boolean for isActive with default true
            $table->timestamps(); // This will add created_at and updated_at timestamp fields
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
