<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInstanciaToJobLogsTable extends Migration
{
    public function up()
    {
        Schema::table('job_logs', function (Blueprint $table) {
            $table->string('instancia')->nullable()->after('numero');
        });
    }

    public function down()
    {
        Schema::table('job_logs', function (Blueprint $table) {
            $table->dropColumn('instancia');
        });
    }
};
