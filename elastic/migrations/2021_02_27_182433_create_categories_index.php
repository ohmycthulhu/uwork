<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateCategoriesIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
      $this->down();
        Index::createIfNotExists('categories', function (Mapping $mapping, Settings $settings) {
          $mapping->wildcard('name');
          $mapping->keyword('id');
          $mapping->keyword('parent_id');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('categories');
    }
}
