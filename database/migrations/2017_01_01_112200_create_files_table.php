<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('type_id');
            $table->foreign('type_id')->references('id')->on('file_types');

            $table->nullableMorphs('attachable');

            $table->string('original_name')->index();
            $table->string('saved_name');
            $table->integer('size');
            $table->string('mime_type')->nullable();

            $table->boolean('is_public');

            $table->integer('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->references('id')->on('users');

            $table->timestamps();

            $table->index('created_at');
            $table->index(['type_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
};
