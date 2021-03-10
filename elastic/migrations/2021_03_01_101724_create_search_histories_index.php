<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateSearchHistoriesIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {

      $this->down();
      Index::createIfNotExists('search_histories', function (Mapping $mapping, Settings $settings) {
        $mapping->text('text');
        $mapping->keyword('weight');
      });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
      Index::dropIfExists('search_histories');
    }
}
