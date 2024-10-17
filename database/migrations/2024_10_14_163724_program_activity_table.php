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
        if (!Schema::hasTable('activity')) {
            Schema::create('activity', function (Blueprint $table) {
                $table->uuid('uuid')->primary();
                $table->uuid('code')->unique();
                $table->uuid('program_id');
                $table->string('name', 255); // Tidak nullable
                $table->uuid('school_id')->nullable(); // Masih nullable
                $table->uuid('partner_id'); // Tidak nullable
                $table->uuid('teacher_id'); // Tidak nullable
                $table->string('description', 255); // Tidak nullable
                $table->date('start_date'); // Tidak nullable
                $table->date('end_date'); // Tidak nullable

                // Tracking fields
                $table->timestamps();               // created_at & updated_at
                $table->softDeletes();              // deleted_at
                $table->uuid('created_by')->nullable(); // created_by
                $table->uuid('updated_by')->nullable(); // updated_by
                $table->uuid('deleted_by')->nullable(); // deleted_by

                $table->foreign('program_id')->references('uuid')->on('internships');
                $table->foreign('school_id')->references('uuid')->on('school');
                $table->foreign('partner_id')->references('uuid')->on('partners');
                $table->foreign('teacher_id')->references('uuid')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity');
    }
};
