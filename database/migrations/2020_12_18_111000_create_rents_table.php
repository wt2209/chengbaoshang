<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 租金表 用于记录预交费（外部单位）的公司房租的记录
        Schema::create('rents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('record_id')->comment('属于的入住记录');
            $table->unsignedBigInteger('company_id'); // 所属公司
            $table->unsignedBigInteger('room_id'); // 所属房间
            $table->string('company_name')->comment('生成租金时的公司名字');
            $table->decimal('money', 10, 2)->comment('租金数额');
            $table->unsignedSmallInteger('year')->comment('年度');
            $table->unsignedTinyInteger('month')->comment('月度');
            $table->date('start_date')->comment('租金开始日期');
            $table->date('end_date')->comment('租金结束日期');
            $table->date('charged_at')->nullable()->comment('预付费的交费时间');
            $table->boolean('is_refund')->default(false)->comment('是否是退费（外部单位退费）');
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
        Schema::dropIfExists('rents');
    }
}
