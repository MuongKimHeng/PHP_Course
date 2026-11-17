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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40)->unique();
            $table->timestamps();
        });

        Schema::create('block_tag', function(Blueprint $table){
            $table->foreignId('block_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags', 'id')->cascadeOnDelete();
            $table->primary(['block_id', 'tag_id']); //each pair only once
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_tag');
        Schema::dropIfExists('tags');
        
    }
};
