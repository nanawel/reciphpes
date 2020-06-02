<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200601163528 extends AbstractMigration
{
    public function getDescription(): string {
        return '';
    }

    public function up(Schema $schema): void {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'sqlite',
            'Migration can only be executed safely on \'sqlite\'.'
        );

        $this->addSql(
            'CREATE TABLE ingredient (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)'
        );
        $this->addSql('CREATE INDEX INGREDIENT_NAME_IDX ON ingredient (name)');
        $this->addSql('CREATE TABLE tag (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX TAG_NAME_IDX ON tag (name)');
        $this->addSql(
            'CREATE TABLE recipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, location_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, location_details VARCHAR(255) NOT NULL, instructions CLOB NOT NULL, created_at DATETIME NOT NULL)'
        );
        $this->addSql('CREATE INDEX IDX_DA88B13764D218E ON recipe (location_id)');
        $this->addSql('CREATE INDEX RECIPE_NAME_IDX ON recipe (name)');
        $this->addSql('CREATE INDEX RECIPE_INSTRUCTIONS_IDX ON recipe (instructions)');
        $this->addSql(
            'CREATE TABLE recipe_tag (recipe_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(recipe_id, tag_id))'
        );
        $this->addSql('CREATE INDEX IDX_72DED3CF59D8A214 ON recipe_tag (recipe_id)');
        $this->addSql('CREATE INDEX IDX_72DED3CFBAD26311 ON recipe_tag (tag_id)');
        $this->addSql(
            'CREATE TABLE recipe_ingredient (recipe_id INTEGER NOT NULL, ingredient_id INTEGER NOT NULL, PRIMARY KEY(recipe_id, ingredient_id))'
        );
        $this->addSql('CREATE INDEX IDX_22D1FE1359D8A214 ON recipe_ingredient (recipe_id)');
        $this->addSql('CREATE INDEX IDX_22D1FE13933FE08C ON recipe_ingredient (ingredient_id)');
        $this->addSql(
            'CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL)'
        );
        $this->addSql('CREATE INDEX LOCATION_NAME_IDX ON location (name)');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'sqlite',
            'Migration can only be executed safely on \'sqlite\'.'
        );

        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_tag');
        $this->addSql('DROP TABLE recipe_ingredient');
        $this->addSql('DROP TABLE location');
    }
}
