<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilityChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 青武公寓水电充值表，用于记录充值记录和数据统计
        Schema::create('utility_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('room_id');
            $table->decimal('electric_amount')->default(0)->comment('充值的电量');
            $table->decimal('electric_money')->default(0)->comment('充值的电费');
            $table->decimal('water_amount')->default(0)->comment('充值的水量');
            $table->decimal('water_money')->default(0)->comment('充值的水费');
            $table->date('charged_at')->comment('充值时间');
            $table->string('charger')->nullable()->comment('充值人');
            $table->string('charger_phone')->nullable()->comment('充值人的电话');
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
        Schema::dropIfExists('utility_charges');
    }
}
