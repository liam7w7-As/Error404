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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string("nombre", 600);
            $table->string("fono");
            $table->string("razon_social")->nullable();
            $table->string("nit_ci")->nullable();
            $table->string("dir", 900);
            $table->string("latitud");
            $table->string("longitud");
            $table->unsignedBigInteger("user_id");
            $table->date("fecha_registro")->nullable();
            $table->integer("status");
            $table->timestamps();

            $table->foreign("user_id")->on("users")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
