<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSenhasToCertificationsTable extends Migration
{
    public function up()
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->string('senhas')->nullable();
        });
    }

    public function down()
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropColumn('senhas'); 
        });
    }
}
