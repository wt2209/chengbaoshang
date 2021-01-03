<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 其他费用表
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->index();
            $table->string('location')->nullable()->comment('房间号/位置');
            $table->string('type')->comment('费用类型');
            $table->decimal('money')->comment('金额');
            $table->string('description')->nullable()->comment('费用说明');
            $table->string('remark')->nullable()->comment('备注');
            $table->date('charged_at')->nullable()->comment('缴费时间');
            $table->string('charge_way')->nullable()->comment('缴费方式');
            $table->string('charger')->nullable()->comment('缴费人');
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
        Schema::dropIfExists('bills');
    }
}
