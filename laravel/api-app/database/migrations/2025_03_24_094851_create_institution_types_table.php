<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('institution_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // desa, kelurahan, kota, kabupaten, provinsi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_types');
    }
};
