<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pengguna (admin & kasir) serta tabel sesi autentikasi.
     */
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama lengkap pengguna');
            $table->string('username')->unique()->comment('Username unik untuk login');
            $table->string('password', 255)->comment('Password terenkripsi (bcrypt)');
            $table->enum('role', ['admin', 'kasir'])->comment('Peran akses pengguna');
            $table->string('security_question')->nullable()->comment('Pertanyaan keamanan untuk reset password mandiri');
            $table->string('security_answer')->nullable()->comment('Jawaban keamanan terenkripsi (bcrypt)');
            $table->timestamps();
            $table->softDeletes();

            $table->index('role');
            $table->index('created_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('pengguna');
    }
};
