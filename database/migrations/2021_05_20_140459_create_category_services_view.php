<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryServicesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      \Illuminate\Support\Facades\DB::statement("
        CREATE OR REPLACE
            VIEW category_services AS
        SELECT
            c.*
        FROM categories c
        LEFT JOIN categories c1
            ON c1.parent_id = c.id
        WHERE c1.id IS NULL;
      ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      \Illuminate\Support\Facades\DB::statement("DROP VIEW IF EXISTS category_services");
    }
}
