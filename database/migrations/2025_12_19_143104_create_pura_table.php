
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('puras', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pura');
            $table->string('slug')->unique();
            $table->string('gambar_card'); // thumbnail/cover
            $table->text('deskripsi_singkat');
            $table->text('sejarah');
            $table->text('lokasi_iframe')->nullable();
            $table->foreignId('kabupaten_id')->constrained('kabupatens');
            $table->foreignId('kategori_pura_id')->constrained('kategori_puras');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('puras');
    }
};