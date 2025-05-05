<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('tpengguna', function (Blueprint $table) {
        $table->id(); // otomatis increment
        $table->string('namapengguna');
        $table->string('katakunci'); // nanti akan di-hash
        $table->boolean('status')->default(1); // 1 aktif, 0 tidak aktif
        $table->string('tipe')->default('admin'); // fix 'admin'
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tpengguna');
    }
};
