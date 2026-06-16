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
        Schema::create('configuracions', function (Blueprint $table) {
            $table->id();
            $table->string("nombre_sistema", 255);
            $table->string("alias", 255);
            $table->string("logo")->nullable();
            $table->string("actividad", 255);
            $table->time("b_hora_inicio_admin")->nullable();
            $table->time("b_hora_fin_admin")->nullable();
            $table->time("b_hora_inicio_dist")->nullable();
            $table->time("b_hora_fin_dist")->nullable();
            $table->time("b_hora_inicio_ven")->nullable();
            $table->time("b_hora_fin_ven")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracions');
    }
};
