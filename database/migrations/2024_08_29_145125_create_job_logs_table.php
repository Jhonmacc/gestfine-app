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
        schema::create('job_logs', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->string('email')->nullable();
            $table->string('certificado')->nullable();
            $table->timestamps();});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_logs');
    }
};
