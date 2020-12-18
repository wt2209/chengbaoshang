<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('area');
            $table->string('title', 20)->unique();
            $table->string('building', 10)->default('');
            $table->string('unit', 20)->default('');
            $table->integer('default_number')->default(8)->comment('房间默认人数');
            $table->decimal('default_deposit')->default(0)->comment('房间默认押金');
            $table->decimal('default_rent')->default(0)->comment('房间默认租金');
            $table->string('remark')->nullable();
            $table->boolean('is_using')->default(true)->comment('是否启用');
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
        Schema::dropIfExists('rooms');
    }
}
