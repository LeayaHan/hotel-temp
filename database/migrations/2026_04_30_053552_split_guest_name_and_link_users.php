<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Split full_name into first_name, middle_initial, last_name on guests table
        Schema::table('guests', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('middle_initial', 5)->nullable()->after('first_name');
            $table->string('last_name')->nullable()->after('middle_initial');
        });

        // 2. Migrate existing full_name data into first_name / last_name
        DB::table('guests')->get()->each(function ($guest) {
            $parts = explode(' ', trim($guest->full_name), 2);
            DB::table('guests')->where('id', $guest->id)->update([
                'first_name' => $parts[0] ?? '',
                'last_name'  => $parts[1] ?? '',
            ]);
        });

        // 3. Add guest_id foreign key to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('guest_id')->nullable()->after('department')
                  ->constrained('guests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['guest_id']);
            $table->dropColumn('guest_id');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_initial', 'last_name']);
        });
    }
};