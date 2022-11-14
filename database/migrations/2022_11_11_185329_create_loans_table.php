<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->Integer('customer_id');
            $table->double('loan_amount');
            $table->Integer('loan_term');
            $table->timestamp('loan_date');
            $table->string('status')->default('PENDING');
            $table->timestamp('approved_reject_date')->nullable();
            $table->double('repaid_loan_amount')->default(0);
            $table->timestamp('loan_repaid_date')->nullable();
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
        Schema::dropIfExists('loans');
    }
}
