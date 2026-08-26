<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('prefijo')->nullable()->after('name');
            $table->string('apellidos')->nullable()->after('prefijo');
            $table->string('puesto')->nullable()->after('apellidos');
            $table->string('imagen_firma')->nullable()->after('puesto');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['prefijo', 'apellidos', 'puesto', 'imagen_firma']);
        });
    }
};