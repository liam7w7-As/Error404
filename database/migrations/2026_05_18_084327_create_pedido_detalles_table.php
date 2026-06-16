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
        Schema::create('pedido_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("pedido_id");
            $table->unsignedBigInteger("producto_id");
            $table->unsignedBigInteger("presentacion_producto_id");
            $table->double("cantidad", 8, 2);
            $table->double("cantidad_total", 8, 2);
            $table->double("cantidad_despacho", 8, 2);
            $table->double("cantidad_entregado", 8, 2);
            $table->double("cantidad_devolucion", 8, 2)->default(0);
            $table->decimal("precio", 24, 2);
            $table->decimal("subtotal", 24, 2);
            $table->integer("status")->default(1);
            $table->timestamps();

            $table->foreign("pedido_id")->on("pedidos")->references("id");
            $table->foreign("producto_id")->on("productos")->references("id");
            $table->foreign("presentacion_producto_id")->on("presentacion_productos")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_detalles');
    }
};
