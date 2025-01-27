<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('envia_email_parametro', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable(false);
            $table->string('titulo')->nullable(false);
            $table->text('mensagem')->nullable(false);
            $table->boolean('status')->default(0); // 0 para Inativo, 1 para Ativo
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envia_email_parametro');
    }
};
