<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230418090145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984584665A');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A80EF684');
        $this->addSql('DROP INDEX IDX_F5299398A80EF684 ON `order`');
        $this->addSql('DROP INDEX IDX_F52993984584665A ON `order`');
        $this->addSql('ALTER TABLE `order` ADD cart_data JSON NOT NULL, ADD total DOUBLE PRECISION NOT NULL, DROP product_id, DROP product_variant_id, DROP quantity');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD product_id INT NOT NULL, ADD product_variant_id INT DEFAULT NULL, ADD quantity INT NOT NULL, DROP cart_data, DROP total');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('CREATE INDEX IDX_F5299398A80EF684 ON `order` (product_variant_id)');
        $this->addSql('CREATE INDEX IDX_F52993984584665A ON `order` (product_id)');
    }
}
