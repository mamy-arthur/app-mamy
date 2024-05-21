<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517122851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE messaging_api__emailing_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE messaging_api__fixtures_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE messaging_api__notices_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE messaging_api__emailing (id INT NOT NULL, from_user VARCHAR(255) NOT NULL, to_user VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, content VARCHAR(1025) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE messaging_api__fixtures (id INT NOT NULL, fixture VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CD9B970D5E540EE ON messaging_api__fixtures (fixture)');
        $this->addSql('COMMENT ON COLUMN messaging_api__fixtures.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE messaging_api__notices (id INT NOT NULL, message TEXT NOT NULL, start_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, end_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, type VARCHAR(130) DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN messaging_api__notices.start_date IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messaging_api__notices.end_date IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messaging_api__notices.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messaging_api__notices.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE messaging_api__emailing_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE messaging_api__fixtures_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE messaging_api__notices_id_seq CASCADE');
        $this->addSql('DROP TABLE messaging_api__emailing');
        $this->addSql('DROP TABLE messaging_api__fixtures');
        $this->addSql('DROP TABLE messaging_api__notices');
    }
}
