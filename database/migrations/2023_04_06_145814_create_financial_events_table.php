<?php

use App\Models\FinancialEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_events', function (Blueprint $table) {
            $table->id();

            $table->string('partner_type')->default(FinancialEvent::PARTNER_TYPE_ES);
            $table->bigInteger('amount');

            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies');

            $table->foreign('admin_id')
                ->references('id')
                ->on('administrators_to_moderators_pivot');

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
        Schema::dropIfExists('financial_events');
    }
};
