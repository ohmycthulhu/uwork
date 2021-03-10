<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateProfileSpecialitiesIndex implements MigrationInterface
{
  /**
   * Run the migration.
   */
  public function up(): void
  {
    $this->down();
    Index::createIfNotExists('profile_specialities', function (Mapping $mapping, Settings $settings) {
      $mapping->keyword('id');
      $mapping->keyword('parent_id');
      $mapping->keyword('user_id');
      $mapping->wildcard('cat_path');

      $mapping->keyword('region_id');
      $mapping->keyword('city_id');
      $mapping->keyword('district_id');

      $mapping->float('price');
    });
  }

  /**
   * Reverse the migration.
   */
  public function down(): void
  {
    Index::dropIfExists('profile_specialities');
  }
}
