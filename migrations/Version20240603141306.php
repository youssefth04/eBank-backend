<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240603141306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session ADD user_id_id INT NOT NULL, ADD session_token VARCHAR(255) NOT NULL, ADD expiration_date DATETIME NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D49D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_D044D5D49D86650F ON session (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D49D86650F');
        $this->addSql('DROP INDEX IDX_D044D5D49D86650F ON session');
        $this->addSql('ALTER TABLE session DROP user_id_id, DROP session_token, DROP expiration_date, CHANGE id id INT NOT NULL');
    }
}
