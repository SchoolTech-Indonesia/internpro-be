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
        if (!Schema::hasTable('opportunities')) {
            Schema::create('opportunities', function (Blueprint $table) {
                $table->string('opportunity_id')->primary();
                $table->string('code')->unique();
                $table->string('program_id')->nullable();
                $table->string('activity_id')->nullable();
                $table->string('name');
                $table->integer('quota');
                $table->string('description');
                $table->string('school_id', 255);
                $table->foreign('school_id')->references('uuid')->on('school')->onDelete('cascade')->onUpdate('cascade');
                $table->char('mentor_id', 36);
                $table->foreign('mentor_id')->references('uuid')->on('users')->onDelete('cascade')->onUpdate('cascade');
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->string('deleted_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
