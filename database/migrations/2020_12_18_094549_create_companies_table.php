<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('category_id')->comment('公司默认分类');
            $table->string('company_name');
            $table->string('business')->nullable()->comment('业务范围');
            $table->string('manager', 4)->nullable()->comment('管理员');
            $table->string('manager_phone', 13)->nullable()->comment('管理员电话');
            $table->string('linkman', 4)->nullable()->comment('日常联系人');
            $table->string('linkman_phone', 13)->nullable()->comment('联系人电话');
            $table->string('remark')->nullable();
            $table->date('lease_start')->nullable(); // 用于外部单位等有租期的公司
            $table->date('lease_end')->nullable();
            $table->index('company_name');
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
        Schema::dropIfExists('companies');
    }
}
