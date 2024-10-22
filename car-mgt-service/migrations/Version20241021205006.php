<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241021205006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cars (id INT AUTO_INCREMENT NOT NULL, make VARCHAR(100) NOT NULL, model VARCHAR(100) NOT NULL, registration_number VARCHAR(20) NOT NULL, insurance_insurer VARCHAR(100) NOT NULL, insurance_policy_number VARCHAR(50) NOT NULL, insurance_date_issued DATE NOT NULL, insurance_date_expiry DATE NOT NULL, insurance_date_start DATE NOT NULL, fitness_issued DATETIME NOT NULL, fitness_valid_until DATETIME NOT NULL, road_tax_issued DATETIME NOT NULL, road_tax_valid_until DATETIME NOT NULL, UNIQUE INDEX UNIQ_95C71D1438CEDFBE (registration_number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_histories (id INT AUTO_INCREMENT NOT NULL, car_id INT NOT NULL, description VARCHAR(255) NOT NULL, service_date DATE NOT NULL, INDEX IDX_9760F4E9C3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_histories ADD CONSTRAINT FK_9760F4E9C3C6F69F FOREIGN KEY (car_id) REFERENCES cars (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_histories DROP FOREIGN KEY FK_9760F4E9C3C6F69F');
        $this->addSql('DROP TABLE cars');
        $this->addSql('DROP TABLE service_histories');
    }
}
