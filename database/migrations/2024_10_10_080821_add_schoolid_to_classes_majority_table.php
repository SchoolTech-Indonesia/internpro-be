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
        Schema::table('classes', function (Blueprint $table) {
            $table->uuid('school_id')->after('class_name');
            $table->foreign('school_id')->references('uuid')->on('school');
            // $table->createdBy();
        });
        Schema::table('majors', function (Blueprint $table) {
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('deleted_by');
        });
        Schema::table('majors', function (Blueprint $table) {
            $table->uuid('school_id')->after('major_name');
            $table->foreign('school_id')->references('uuid')->on('school');
            $table->createdBy();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
            $table->dropCreatedBy();
        });

        Schema::table('majors', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
            $table->dropCreatedBy();
        });
    }
};
