<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627080453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE conseil_weather (conseil_id INT NOT NULL, weather_id INT NOT NULL, INDEX IDX_12841842668A3E03 (conseil_id), INDEX IDX_128418428CE675E (weather_id), PRIMARY KEY(conseil_id, weather_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conseil_weather ADD CONSTRAINT FK_12841842668A3E03 FOREIGN KEY (conseil_id) REFERENCES conseil (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conseil_weather ADD CONSTRAINT FK_128418428CE675E FOREIGN KEY (weather_id) REFERENCES weather (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE conseil_weather DROP FOREIGN KEY FK_12841842668A3E03
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conseil_weather DROP FOREIGN KEY FK_128418428CE675E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE conseil_weather
        SQL);
    }
}
