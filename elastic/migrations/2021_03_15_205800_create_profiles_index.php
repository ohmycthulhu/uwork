<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateProfilesIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
      $this->down();
      Index::create('profiles', function (Mapping $mapping, Settings $settings) {
        $mapping->keyword("id");
        $mapping->keyword("regionId");
        $mapping->keyword("cityId");
        $mapping->keyword("districtId");
        $mapping->keyword("district");
        $mapping->float("rating");
        $mapping->keyword('userId');
        $mapping->object("specialities");
        $mapping->float('price_min');
        $mapping->float('price_max');
        $mapping->float('price_avg');

        $mapping->keyword('isConfirmed');
      });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
      Index::dropIfExists('profiles');
    }
}
