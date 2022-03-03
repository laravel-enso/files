<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('favorite_files', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('file_id')->index();
            $table->foreign('file_id')->references('id')->on('files')
                ->onUpdate('restrict')->onDelete('restrict');

            $table->timestamps();

            $table->unique(['user_id', 'file_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorite_files');
    }
};
