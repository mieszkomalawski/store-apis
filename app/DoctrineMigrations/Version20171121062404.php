<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171121062404 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $file = __DIR__ . '/../../vendor/prooph/pdo-event-store/scripts/mysql/01_event_streams_table.sql';
        $sql = file_get_contents($file);
        $this->addSql($sql);

        $file = __DIR__ . '/../../vendor/prooph/pdo-event-store/scripts/mysql/02_projections_table.sql';
        $sql = file_get_contents($file);
        $this->addSql($sql);

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `event_streams`');
        $this->addSql('DROP TABLE `projections`');
    }
}
