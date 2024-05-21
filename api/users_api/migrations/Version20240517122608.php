<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517122608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE users_api__fixtures_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_api__services_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_api__users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users_api__fixtures (id INT NOT NULL, fixture VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6480D225E540EE ON users_api__fixtures (fixture)');
        $this->addSql('COMMENT ON COLUMN users_api__fixtures.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE users_api__services (id INT NOT NULL, name VARCHAR(50) NOT NULL, code VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_29C3097F77153098 ON users_api__services (code)');
        $this->addSql('CREATE TABLE users_api__users (id INT NOT NULL, service_id INT DEFAULT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, email VARCHAR(255) NOT NULL, mobile_number VARCHAR(20) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, registration_number VARCHAR(100) DEFAULT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CF27A161E7927C74 ON users_api__users (email)');
        $this->addSql('CREATE INDEX IDX_CF27A161ED5CA9E6 ON users_api__users (service_id)');
        $this->addSql('ALTER TABLE users_api__users ADD CONSTRAINT FK_CF27A161ED5CA9E6 FOREIGN KEY (service_id) REFERENCES users_api__services (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE users_api__users DROP CONSTRAINT FK_CF27A161ED5CA9E6');
        $this->addSql('DROP SEQUENCE users_api__fixtures_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_api__services_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_api__users_id_seq CASCADE');
        $this->addSql('DROP TABLE users_api__fixtures');
        $this->addSql('DROP TABLE users_api__services');
        $this->addSql('DROP TABLE users_api__users');
    }
}
