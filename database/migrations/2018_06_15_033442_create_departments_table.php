<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('部门名称');
            $table->string('description')->nullable(true)->comment('部门描述');
            $table->unsignedInteger('pid')->default(0)->comment('上级部门id');
            $table->string('manager')->nullable(true)->comment('部门主管');
            $table->boolean('status')->default(0)->comment('部门启用状态');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
