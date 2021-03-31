<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundlesLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundles_logs', function (Blueprint $table) {
            $table->id();
            $table->string('BundleNo');
            $table->string('ActionTaken');
            $table->string('Remarks');
            $table->softDeletes();
            $table->timestamps();
            $table->string('updated_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bundles_logs');
    }
}
