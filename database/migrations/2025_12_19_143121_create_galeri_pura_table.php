
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('galeri_puras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pura_id')
                  ->constrained('puras')
                  ->onDelete('cascade');
            $table->string('foto');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('galeri_pura');
    }
};