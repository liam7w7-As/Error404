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
        Schema::create('segmentacion_zonas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("departamento_id");
            $table->unsignedBigInteger("provincia_id");
            $table->unsignedBigInteger("ciudad_id");
            $table->string("zona");
            $table->string("color");
            $table->json("segmentacion");
            $table->timestamps();

            $table->foreign("departamento_id")->on("departamentos")->references("id");
            $table->foreign("provincia_id")->on("provincias")->references("id");
            $table->foreign("ciudad_id")->on("ciudads")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segmentacion_zonas');
    }
};
