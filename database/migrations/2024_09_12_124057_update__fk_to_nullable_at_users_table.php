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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('school_id')->nullable()->change();
            $table->uuid('major_id')->nullable()->change();
            $table->uuid('class_id')->nullable()->change();
            $table->uuid('partner_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('school_id')->nullable(false)->change();
            $table->uuid('major_id')->nullable(false)->change();
            $table->uuid('class_id')->nullable(false)->change();
            $table->uuid('partner_id')->nullable(false)->change();
        });
    }
};