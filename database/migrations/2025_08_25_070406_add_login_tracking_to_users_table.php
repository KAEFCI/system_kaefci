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
            if (!Schema::hasColumn('users','login_status')) {
                $table->string('login_status')->default('offline')->after('password');
            }
            if (!Schema::hasColumn('users','last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('login_status');
            }
            if (!Schema::hasColumn('users','last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable()->after('last_login_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['last_seen_at','last_login_at','login_status'] as $c) {
                if (Schema::hasColumn('users',$c)) $table->dropColumn($c);
            }
        });
    }
};
