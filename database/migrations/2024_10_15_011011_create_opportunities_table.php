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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->string('code', 255)->unique();
            $table->string('program_id', 255);
            $table->string('activity_id', 255);
            $table->string('name', 255);
            $table->integer('quota');
            $table->string('description', 255)->nullable();
            $table->string('school_id', 255);
            $table->string('mentor_id', 255);
            $table->timestamps();
            $table->softDeletes();
            $table->string('created_by', 255);
            $table->string('updated_by', 255)->nullable();
            $table->string('deleted_by', 255)->nullable();

            // Foreign Key Constraints
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('mentor_id')->references('id')->on('mentors')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
