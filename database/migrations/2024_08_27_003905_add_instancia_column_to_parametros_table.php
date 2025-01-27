<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstanciaColumnToParametrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->string('instancia')->nullable()->after('observacao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parametros', function (Blueprint $table) {
            $table->dropColumn('instancia');
        });
    }
}
