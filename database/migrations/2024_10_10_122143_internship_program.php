<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Memeriksa apakah tabel 'internships' sudah ada
        if (!Schema::hasTable('internships')) {
            Schema::create('internships', function (Blueprint $table) {
                $table->uuid('uuid')->primary();
                $table->uuid('code')->unique();
                $table->string('name', 255);
                $table->string('description', 255);
                $table->date('start_date');
                $table->date('end_date');
                $table->uuid('school_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->createdBy();

                $table->foreign('school_id')->references('uuid')->on('school');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
