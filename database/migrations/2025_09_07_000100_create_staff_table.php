<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('role')->default('karyawan');
                $table->string('status')->default('active');
                $table->string('password');
                $table->timestamps();
            });
        }
    }
};
