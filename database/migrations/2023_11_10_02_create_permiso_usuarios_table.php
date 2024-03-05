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
        Schema::create('permiso_usuarios', function (Blueprint $table) {
            $table->id();
            $table->integer("agregar");
            $table->integer("editar");
            $table->integer("eliminar");
            $table->integer("visualizar");
            $table->unsignedBigInteger("tipo_usuario");
            $table->foreign("tipo_usuario")->references("id")->on("tipo_usuarios");
            $table->unsignedBigInteger("modulo_id");
            $table->foreign("modulo_id")->references("id")->on("modulos");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permiso_usuarios');
    }
};
