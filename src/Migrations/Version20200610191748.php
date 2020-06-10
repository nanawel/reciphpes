<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200610191748 extends AbstractMigration
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

        $this->addSql('DROP INDEX IDX_31F2F78B59D8A214');
        $this->addSql('DROP INDEX IDX_31F2F78B933FE08C');
        $this->addSql(
            'CREATE TEMPORARY TABLE __temp__recipeingredient AS SELECT recipe_id, ingredient_id, note FROM recipeingredient'
        );
        $this->addSql('DROP TABLE recipeingredient');
        $this->addSql(
            'CREATE TABLE recipeingredient (recipe_id INTEGER NOT NULL, ingredient_id INTEGER NOT NULL, note VARCHAR(255) DEFAULT NULL COLLATE BINARY, PRIMARY KEY(recipe_id, ingredient_id), CONSTRAINT FK_31F2F78B59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_31F2F78B933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql(
            'INSERT INTO recipeingredient (recipe_id, ingredient_id, note) SELECT recipe_id, ingredient_id, note FROM __temp__recipeingredient'
        );
        $this->addSql('DROP TABLE __temp__recipeingredient');
        $this->addSql('CREATE INDEX IDX_31F2F78B59D8A214 ON recipeingredient (recipe_id)');
        $this->addSql('CREATE INDEX IDX_31F2F78B933FE08C ON recipeingredient (ingredient_id)');
        $this->addSql('DROP INDEX IDX_DA88B13764D218E');
        $this->addSql('DROP INDEX RECIPE_NAME_IDX');
        $this->addSql('DROP INDEX RECIPE_INSTRUCTIONS_IDX');
        $this->addSql(
            'CREATE TEMPORARY TABLE __temp__recipe AS SELECT id, location_id, name, location_details, instructions, created_at FROM recipe'
        );
        $this->addSql('DROP TABLE recipe');
        $this->addSql(
            'CREATE TABLE recipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, location_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, location_details VARCHAR(255) DEFAULT NULL COLLATE BINARY, instructions CLOB DEFAULT NULL COLLATE BINARY, created_at DATETIME NOT NULL, CONSTRAINT FK_DA88B13764D218E FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql(
            'INSERT INTO recipe (id, location_id, name, location_details, instructions, created_at) SELECT id, location_id, name, location_details, instructions, created_at FROM __temp__recipe'
        );
        $this->addSql('DROP TABLE __temp__recipe');
        $this->addSql('CREATE INDEX IDX_DA88B13764D218E ON recipe (location_id)');
        $this->addSql('CREATE INDEX RECIPE_NAME_IDX ON recipe (name)');
        $this->addSql('CREATE INDEX RECIPE_INSTRUCTIONS_IDX ON recipe (instructions)');
        $this->addSql('DROP INDEX IDX_72DED3CF59D8A214');
        $this->addSql('DROP INDEX IDX_72DED3CFBAD26311');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe_tag AS SELECT recipe_id, tag_id FROM recipe_tag');
        $this->addSql('DROP TABLE recipe_tag');
        $this->addSql(
            'CREATE TABLE recipe_tag (recipe_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(recipe_id, tag_id), CONSTRAINT FK_72DED3CF59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_72DED3CFBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql('INSERT INTO recipe_tag (recipe_id, tag_id) SELECT recipe_id, tag_id FROM __temp__recipe_tag');
        $this->addSql('DROP TABLE __temp__recipe_tag');
        $this->addSql('CREATE INDEX IDX_72DED3CF59D8A214 ON recipe_tag (recipe_id)');
        $this->addSql('CREATE INDEX IDX_72DED3CFBAD26311 ON recipe_tag (tag_id)');
        $this->addSql('DROP INDEX IDX_42E0B96924BFD492');
        $this->addSql('DROP INDEX IDX_42E0B96959D8A214');
        $this->addSql(
            'CREATE TEMPORARY TABLE __temp__recipe_timeofyear AS SELECT recipe_id, timeofyear_id FROM recipe_timeofyear'
        );
        $this->addSql('DROP TABLE recipe_timeofyear');
        $this->addSql(
            'CREATE TABLE recipe_timeofyear (recipe_id INTEGER NOT NULL, timeofyear_id INTEGER NOT NULL, PRIMARY KEY(recipe_id, timeofyear_id), CONSTRAINT FK_42E0B96959D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42E0B96924BFD492 FOREIGN KEY (timeofyear_id) REFERENCES timeofyear (id) NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql(
            'INSERT INTO recipe_timeofyear (recipe_id, timeofyear_id) SELECT recipe_id, timeofyear_id FROM __temp__recipe_timeofyear'
        );
        $this->addSql('DROP TABLE __temp__recipe_timeofyear');
        $this->addSql('CREATE INDEX IDX_42E0B96924BFD492 ON recipe_timeofyear (timeofyear_id)');
        $this->addSql('CREATE INDEX IDX_42E0B96959D8A214 ON recipe_timeofyear (recipe_id)');
    }

    public function down(Schema $schema): void {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'sqlite',
            'Migration can only be executed safely on \'sqlite\'.'
        );

        $this->addSql('DROP INDEX IDX_DA88B13764D218E');
        $this->addSql('DROP INDEX RECIPE_NAME_IDX');
        $this->addSql('DROP INDEX RECIPE_INSTRUCTIONS_IDX');
        $this->addSql(
            'CREATE TEMPORARY TABLE __temp__recipe AS SELECT id, location_id, name, location_details, instructions, created_at FROM recipe'
        );
        $this->addSql('DROP TABLE recipe');
        $this->addSql(
            'CREATE TABLE recipe (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, location_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, location_details VARCHAR(255) DEFAULT NULL, instructions CLOB DEFAULT NULL, created_at DATETIME NOT NULL)'
        );
        $this->addSql(
            'INSERT INTO recipe (id, location_id, name, location_details, instructions, created_at) SELECT id, location_id, name, location_details, instructions, created_at FROM __temp__recipe'
        );
        $this->addSql('DROP TABLE __temp__recipe');
        $this->addSql('CREATE INDEX IDX_DA88B13764D218E ON recipe (location_id)');
        $this->addSql('CREATE INDEX RECIPE_NAME_IDX ON recipe (name)');
        $this->addSql('CREATE INDEX RECIPE_INSTRUCTIONS_IDX ON recipe (instructions)');
        $this->addSql('DROP INDEX IDX_72DED3CF59D8A214');
        $this->addSql('DROP INDEX IDX_72DED3CFBAD26311');
        $this->addSql('CREATE TEMPORARY TABLE __temp__recipe_tag AS SELECT recipe_id, tag_id FROM recipe_tag');
        $this->addSql('DROP TABLE recipe_tag');
        $this->addSql(
            'CREATE TABLE recipe_tag (recipe_id INTEGER NOT NULL, tag_id INTEGER NOT NULL, PRIMARY KEY(recipe_id, tag_id))'
        );
        $this->addSql('INSERT INTO recipe_tag (recipe_id, tag_id) SELECT recipe_id, tag_id FROM __temp__recipe_tag');
        $this->addSql('DROP TABLE __temp__recipe_tag');
        $this->addSql('CREATE INDEX IDX_72DED3CF59D8A214 ON recipe_tag (recipe_id)');
        $this->addSql('CREATE INDEX IDX_72DED3CFBAD26311 ON recipe_tag (tag_id)');
        $this->addSql('DROP INDEX IDX_42E0B96959D8A214');
        $this->addSql('DROP INDEX IDX_42E0B96924BFD492');
        $this->addSql(
            'CREATE TEMPORARY TABLE __temp__recipe_timeofyear AS SELECT recipe_id, timeofyear_id FROM recipe_timeofyear'
        );
        $this->addSql('DROP TABLE recipe_timeofyear');
        $this->addSql(
            'CREATE TABLE recipe_timeofyear (recipe_id INTEGER NOT NULL, timeofyear_id INTEGER NOT NULL, PRIMARY KEY(recipe_id, timeofyear_id))'
        );
        $this->addSql(
            'INSERT INTO recipe_timeofyear (recipe_id, timeofyear_id) SELECT recipe_id, timeofyear_id FROM __temp__recipe_timeofyear'
        );
        $this->addSql('DROP TABLE __temp__recipe_timeofyear');
        $this->addSql('CREATE INDEX IDX_42E0B96959D8A214 ON recipe_timeofyear (recipe_id)');
        $this->addSql('CREATE INDEX IDX_42E0B96924BFD492 ON recipe_timeofyear (timeofyear_id)');
        $this->addSql('DROP INDEX IDX_31F2F78B59D8A214');
        $this->addSql('DROP INDEX IDX_31F2F78B933FE08C');
        $this->addSql(
            'CREATE TEMPORARY TABLE __temp__recipeingredient AS SELECT recipe_id, ingredient_id, note FROM recipeingredient'
        );
        $this->addSql('DROP TABLE recipeingredient');
        $this->addSql(
            'CREATE TABLE recipeingredient (recipe_id INTEGER NOT NULL, ingredient_id INTEGER NOT NULL, note VARCHAR(255) DEFAULT NULL, PRIMARY KEY(recipe_id, ingredient_id))'
        );
        $this->addSql(
            'INSERT INTO recipeingredient (recipe_id, ingredient_id, note) SELECT recipe_id, ingredient_id, note FROM __temp__recipeingredient'
        );
        $this->addSql('DROP TABLE __temp__recipeingredient');
        $this->addSql('CREATE INDEX IDX_31F2F78B59D8A214 ON recipeingredient (recipe_id)');
        $this->addSql('CREATE INDEX IDX_31F2F78B933FE08C ON recipeingredient (ingredient_id)');
    }
}
