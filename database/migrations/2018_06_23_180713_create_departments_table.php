<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kalnoy\Nestedset\NestedSet;

class CreateDepartmentsTable extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('departments', function(Blueprint $table) {
      $table->increments('id');
      $table->unsignedInteger('order');
      $table->string('name')->unique();
      NestedSet::columns($table);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('departments');
  }

}
