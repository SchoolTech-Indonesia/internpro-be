<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mentor_partner', function (Blueprint $table) {
            $table->uuid('uuid')->default(DB::raw('(UUID())'))->primary();
            $table->uuid('user_id');
            $table->uuid('partner_id');

            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreign('partner_id')->references('uuid')->on('partners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentor_partner');
    }
};