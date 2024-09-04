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
        Schema::create('partners', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('partner_name', 255);
            $table->string('partner_address', 255);
            $table->binary('partner_logo');
            $table->string('number_sk', 255)->unique();
            $table->datetime('end_date_sk');
            $table->string('school', 255);
            $table->foreign('school')->references('uuid')->on('school');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
