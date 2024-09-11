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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('nip');
            $table->dropColumn('nisn');
            $table->uuid('uuid')->primary()->first();
            $table->string('nip_nisn', 18)->unique()->after('uuid');
            $table->string('phone_number', 15)->unique()->after('email');
            $table->uuid('school_id')->after('role_id');
            $table->uuid('major_id')->after('school_id');
            $table->uuid('class_id')->after('major_id');
            $table->uuid('partner_id')->after('class_id');

            if (!Schema::hasColumn('users', 'created_by')) {
            $table->string('created_by')->nullable()->after('partner_id');
            }
            if(!Schema::hasColumn('users', 'updated_by')){
            $table->string('updated_by')->nullable()->after('created_by');
            }
            if(!Schema::hasColumn('users', 'deleted_by')){
            $table->string('deleted_by')->nullable()->after('updated_by');
            }
            if(!Schema::hasColumn('users', 'deleted_at')){
                $table->softDeletes()->after('deleted_by');
            }

            

            $table->foreign('school_id')->references('uuid')->on('school');
            $table->foreign('major_id')->references('uuid')->on('majors');
            $table->foreign('class_id')->references('uuid')->on('classes');
            $table->foreign('partner_id')->references('uuid')->on('partners');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};