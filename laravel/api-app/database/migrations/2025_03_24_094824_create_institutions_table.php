<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('institution_type_id')->constrained('institution_types')->onDelete('cascade');
            $table->string('location_code')->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->string('alias')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address')->nullable();
            $table->string('letter')->nullable(); // Surat/legalitas
            $table->foreignId('parent_id')->nullable()->constrained('institutions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};

