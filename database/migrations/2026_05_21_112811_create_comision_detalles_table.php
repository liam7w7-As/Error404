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
        Schema::create('comision_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("comision_id");
            $table->unsignedBigInteger("despacho_id");
            $table->unsignedBigInteger("categoria_producto_id");
            $table->unsignedBigInteger("producto_id");
            $table->unsignedBigInteger("presentacion_producto_id");
            $table->double("cantidad", 8, 2);
            $table->doube("p_distribuidor", 8, 2);
            $table->decimal("comision_distribuidor", 24, 2);
            $table->doube("p_vendedor", 8, 2);
            $table->decimal("comision_vendedor", 24, 2);
            $table->decimal("entrega_distribuidor", 24, 2);
            $table->decimal("entrega_vendedor", 24, 2);
            $table->timestamps();

            $table->foreign("comision_id")->on("comisions")->references("id");
            $table->foreign("despacho_id")->on("despachos")->references("id");
            $table->foreign("categoria_producto_id")->on("categoria_productos")->references("id");
            $table->foreign("producto_id")->on("productos")->references("id");
            $table->foreign("presentacion_producto_id")->on("produpresentacion_productos")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comision_detalles');
    }
};
