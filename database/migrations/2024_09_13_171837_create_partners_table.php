<?php

use App\Blueprint\CreatedByBlueprint;
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
            $table->dropForeign(['partner_id']);
            $table->dropColumn('partner_id');
        });
        Schema::dropIfExists('partners');
        Schema::create('partners', function (Blueprint $table) {
            // create uuid with default value
            $table->uuid()->primary();
            $table->string('name', 255)->unique();
            $table->text('address');
            $table->text('logo');
            $table->text('file_sk');
            $table->string('number_sk', 255)->unique();
            $table->dateTime('end_date_sk');
            $table->string('school', 255);
            $table->foreign('school')->references('uuid')->on('school');
            $table->createdBy();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropCreatedBy();
        });

        Schema::dropIfExists('partners');
    }
};
