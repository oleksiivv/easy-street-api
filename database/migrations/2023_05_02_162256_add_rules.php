<?php

use App\Models\Company;
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
        $companies = Company::all()->load('games');
        foreach ($companies as $company) {
            if ($company->games() && $company->games()->count()>0){
                continue;
            } else {
                $company->delete();
            }
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->string('name')->unique()->change();
        });

        \App\Models\User::where('email_is_confirmed', false)->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
