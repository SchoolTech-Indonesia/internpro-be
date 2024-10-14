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
        Schema::create('internship_has_class', function (Blueprint $table) {
            $table->uuid('internship_id');
            $table->uuid('class_id');
            $table->primary(['internship_id', 'class_id']); // Membuat primary key gabungan

            // Foreign key
            $table->foreign('internship_id')->references('uuid')->on('internships')->onDelete('cascade');
            $table->foreign('class_id')->references('uuid')->on('classes')->onDelete('cascade');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_has_class');
    }
};
