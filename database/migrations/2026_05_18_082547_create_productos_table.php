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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string("nombre", 255);
            $table->text("descripcion")->nullable();
            $table->unsignedBigInteger("categoria_producto_id");
            $table->double("stock_min", 8, 2);
            $table->double("stock_actual", 8, 2)->default(0);
            $table->integer("estado")->default(1);
            $table->string("imagen")->nullable();
            $table->timestamps();

            $table->foreign("categoria_producto_id")->on("categoria_productos")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
