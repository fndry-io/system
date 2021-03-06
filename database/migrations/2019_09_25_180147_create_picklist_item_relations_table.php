<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePicklistItemRelationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('picklist_item_relations', function(Blueprint $table)
		{
			$table->integer('picklist_item_id');
			$table->morphs('relatable');
            $table->foreign('picklist_item_id')->references('id')->on('picklist_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('picklist_item_relations');
	}

}
