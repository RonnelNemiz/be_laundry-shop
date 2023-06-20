<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCategoryUserTableAndColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('category_user', 'order_details');

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn([
                'user_id', 'kilo'
            ]);

            $table->string('weight')->after('quantity');
            $table->text('items')->after('quantity');
            $table->boolean('status')->after('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('order_details', 'category_user');

        Schema::table('category_user', function (Blueprint $table) {
            $table->dropColumn([
                'weight', 'items', 'status'
            ]);

            $table->bigInteger('user_id');
            $table->string('kilo');
        });
    }
}
