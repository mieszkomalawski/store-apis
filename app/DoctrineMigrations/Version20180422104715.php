<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180422104715 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE _4228e4a00331b5d5e751db0481828e22a2c3c8ef');
        $this->addSql('DROP TABLE event_streams');
        $this->addSql('DROP TABLE projections');
        $this->addSql('ALTER TABLE products ADD priority INT NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE _4228e4a00331b5d5e751db0481828e22a2c3c8ef (no BIGINT AUTO_INCREMENT NOT NULL, event_id CHAR(36) NOT NULL COLLATE utf8_bin, event_name VARCHAR(100) NOT NULL COLLATE utf8_bin, payload JSON NOT NULL, metadata JSON NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX ix_event_id (event_id), PRIMARY KEY(no)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_streams (no BIGINT AUTO_INCREMENT NOT NULL, real_stream_name VARCHAR(150) NOT NULL COLLATE utf8_bin, stream_name CHAR(41) NOT NULL COLLATE utf8_bin, metadata JSON DEFAULT NULL, category VARCHAR(150) DEFAULT NULL COLLATE utf8_bin, UNIQUE INDEX ix_rsn (real_stream_name), INDEX ix_cat (category), PRIMARY KEY(no)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projections (no BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL COLLATE utf8_bin, position JSON DEFAULT NULL, state JSON DEFAULT NULL, status VARCHAR(28) NOT NULL COLLATE utf8_bin, locked_until CHAR(26) DEFAULT NULL COLLATE utf8_bin, UNIQUE INDEX ix_name (name), PRIMARY KEY(no)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE products DROP priority');
    }
}
