<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517122305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE auth_api__credentials_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE auth_api__fixtures_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE auth_api__permissions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE auth_api__permissions_types_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE auth_api__roles_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE auth_api__credentials (id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, password_reset VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F67FA8C7F85E0677 ON auth_api__credentials (username)');
        $this->addSql('CREATE TABLE auth_api__users_roles (user_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(user_id, role_id))');
        $this->addSql('CREATE INDEX IDX_5D330A47A76ED395 ON auth_api__users_roles (user_id)');
        $this->addSql('CREATE INDEX IDX_5D330A47D60322AC ON auth_api__users_roles (role_id)');
        $this->addSql('CREATE TABLE auth_api__fixtures (id INT NOT NULL, fixture VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83ED93F5E540EE ON auth_api__fixtures (fixture)');
        $this->addSql('COMMENT ON COLUMN auth_api__fixtures.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('CREATE TABLE auth_api__permissions (id INT NOT NULL, permission_type_id INT DEFAULT NULL, actions TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_21974CA6F25D6DC4 ON auth_api__permissions (permission_type_id)');
        $this->addSql('COMMENT ON COLUMN auth_api__permissions.actions IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE auth_api__permissions_types (id INT NOT NULL, resource_type VARCHAR(50) NOT NULL, resource VARCHAR(255) DEFAULT NULL, possibles_actions TEXT NOT NULL, related_perm_types TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX resource_unique ON auth_api__permissions_types (resource_type, resource)');
        $this->addSql('COMMENT ON COLUMN auth_api__permissions_types.possibles_actions IS \'(DC2Type:simple_array)\'');
        $this->addSql('COMMENT ON COLUMN auth_api__permissions_types.related_perm_types IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE auth_api__roles (id INT NOT NULL, name VARCHAR(50) NOT NULL, code VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A4F4886F77153098 ON auth_api__roles (code)');
        $this->addSql('CREATE TABLE auth_api__roles_permissions (role_id INT NOT NULL, permission_id INT NOT NULL, PRIMARY KEY(role_id, permission_id))');
        $this->addSql('CREATE INDEX IDX_445BA631D60322AC ON auth_api__roles_permissions (role_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_445BA631FED90CCA ON auth_api__roles_permissions (permission_id)');
        $this->addSql('ALTER TABLE auth_api__users_roles ADD CONSTRAINT FK_5D330A47A76ED395 FOREIGN KEY (user_id) REFERENCES auth_api__credentials (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_api__users_roles ADD CONSTRAINT FK_5D330A47D60322AC FOREIGN KEY (role_id) REFERENCES auth_api__roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_api__permissions ADD CONSTRAINT FK_21974CA6F25D6DC4 FOREIGN KEY (permission_type_id) REFERENCES auth_api__permissions_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_api__roles_permissions ADD CONSTRAINT FK_445BA631D60322AC FOREIGN KEY (role_id) REFERENCES auth_api__roles (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auth_api__roles_permissions ADD CONSTRAINT FK_445BA631FED90CCA FOREIGN KEY (permission_id) REFERENCES auth_api__permissions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE auth_api__users_roles DROP CONSTRAINT FK_5D330A47A76ED395');
        $this->addSql('ALTER TABLE auth_api__roles_permissions DROP CONSTRAINT FK_445BA631FED90CCA');
        $this->addSql('ALTER TABLE auth_api__permissions DROP CONSTRAINT FK_21974CA6F25D6DC4');
        $this->addSql('ALTER TABLE auth_api__users_roles DROP CONSTRAINT FK_5D330A47D60322AC');
        $this->addSql('ALTER TABLE auth_api__roles_permissions DROP CONSTRAINT FK_445BA631D60322AC');
        $this->addSql('DROP SEQUENCE auth_api__credentials_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE auth_api__fixtures_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE auth_api__permissions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE auth_api__permissions_types_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE auth_api__roles_id_seq CASCADE');
        $this->addSql('DROP TABLE auth_api__credentials');
        $this->addSql('DROP TABLE auth_api__users_roles');
        $this->addSql('DROP TABLE auth_api__fixtures');
        $this->addSql('DROP TABLE auth_api__permissions');
        $this->addSql('DROP TABLE auth_api__permissions_types');
        $this->addSql('DROP TABLE auth_api__roles');
        $this->addSql('DROP TABLE auth_api__roles_permissions');
    }
}
