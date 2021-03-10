<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateMessagesIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
      $this->down();
        //
      Index::createIfNotExists('messages', function (Mapping $mapping, Settings $settings) {
        $mapping->keyword('id');
        $mapping->keyword('text');
        $mapping->integer('chat_id');
      });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('messages');
    }
}
