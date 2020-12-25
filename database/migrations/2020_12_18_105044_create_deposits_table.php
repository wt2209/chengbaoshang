<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 押金表
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('record_id');
            $table->decimal('money');
            $table->string('company_name')->comment('开单子时公司的名字');
            $table->date('billed_at')->comment('开单子的时间');
            $table->date('charged_at')->nullable()->comment('交费时间/财务扣款时间');
            $table->string('charge_way')->nullable()->comment('缴费方式');
            $table->string('refund_company_name')->nullable()->comment('退房时的公司名字');
            $table->date('refunded_at')->nullable()->comment('退费时间（退房）');
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
        Schema::dropIfExists('deposits');
    }
}
