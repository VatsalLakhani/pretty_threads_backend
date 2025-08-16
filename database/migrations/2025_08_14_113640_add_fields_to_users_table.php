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
            $table->string('phone_number')->after('email');
            $table->text('full_address')->after('phone_number');
            $table->string('city')->after('full_address');
            $table->string('pincode', 10)->after('city');
            $table->renameColumn('name', 'full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'full_address', 'city', 'pincode']);
            $table->renameColumn('full_name', 'name');
        });
    }
};
