<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string("isbn",15)->unique();
            $table->string("title",255);
            $table->text("description")->nullable();
            $table->date("published_date")->nullable();
            $table->unsignedBigInteger("category_id"); //unsigned para guardar un valor entero, sin valor default, permitiendo el match con la table
            $table->unsignedBigInteger("editorial_id");//unsigned permite match a la fk, sin importar el tamaño, lo adapta
        });

        Schema::table('books', function (Blueprint $table){
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('editorial_id')->references('id')->on('editorials');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
