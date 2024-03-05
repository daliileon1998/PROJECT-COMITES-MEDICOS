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
        Schema::create('gestion_comites', function (Blueprint $table) {
            $table->id();
            $table->date("fecha_comite");
            $table->unsignedBigInteger("comite_id");
            $table->foreign("comite_id")->references("id")->on("comites");
            $table->text("adjunto")->nullable();
            $table->text("acta")->nullable();
            $table->text("descripcion");
            $table->smallInteger("estado");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gestion_comites');
    }
};
