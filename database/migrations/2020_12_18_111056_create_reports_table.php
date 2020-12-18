<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 财务报表 用于扣款或交费依据
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('record_id');
            $table->string('company_name')->comment('当前的公司名称');
            $table->date('start_date')->comment('开始时间');
            $table->date('end_date')->comment('结束时间');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('pre_electric_base');
            $table->unsignedInteger('current_electric_base');
            $table->unsignedInteger('electric_amount')->comment('本期用电量');
            $table->decimal('electric_price')->nullable()->comment('电费单价');
            $table->decimal('electric_money', 10, 2)->comment('本期电费');
            $table->unsignedInteger('pre_water_base');
            $table->unsignedInteger('current_water_base');
            $table->unsignedInteger('water_amount')->default(0)->comment('本期用水量');
            $table->decimal('water_price')->nullable()->comment('水费单价');
            $table->decimal('water_money', 10, 2)->default(0)->comment('本期水费');
            $table->decimal('rent', 10, 2)->default(0)->comment('本期租金');
            $table->decimal('rent_discount', 4, 2)->default(0)->comment('本期减免比例');
            $table->decimal('actual_rent', 10, 2)->default(0)->comment('本期实际租金');
            $table->date('charged_at')->nullable()->comment('交费/扣款时间');
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
        Schema::dropIfExists('reports');
    }
}
