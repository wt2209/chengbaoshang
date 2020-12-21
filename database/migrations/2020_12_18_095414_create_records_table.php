<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('category_id')->nullable();
            $table->unsignedInteger('room_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('gender');
            $table->string('company_name')->default('')->comment('初始入住时公司的名字（改名前）');
            $table->decimal('rent')->comment('正式租金');
            $table->boolean('is_living')->default(true)->comment('正常居住？退房？');
            $table->date('entered_at')->nullable()->comment('入住时间');
            $table->date('quit_at')->nullable()->comment('退房时间');
            $table->boolean('has_lease')->default(false)->comment('是否存在租期');
            $table->date('lease_start')->nullable()->comment('租期开始日期');
            $table->date('lease_end')->nullable()->comment('租期结束日期');
            $table->integer('electric_start_base')->default(0);
            $table->integer('electric_end_base')->default(0);
            $table->integer('water_start_base')->default(0);
            $table->integer('water_end_base')->default(0);
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
        Schema::dropIfExists('records');
    }
}
