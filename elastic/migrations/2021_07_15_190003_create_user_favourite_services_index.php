<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateUserFavouriteServicesIndex implements MigrationInterface
{
  protected $index = 'user_favourite_services';
    /**
     * Run the migration.
     */
    public function up(): void
    {
        $this->down();
        Index::create($this->index, function (Mapping $mapping, Settings $settings) {
          $mapping->keyword("id");
          $mapping->keyword("userId");
          $mapping->object("speciality");
          $mapping->object('profile');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
      Index::dropIfExists($this->index);
    }
}
