<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517145649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pedidos (id INT AUTO_INCREMENT NOT NULL, cpf_cliente_id INT DEFAULT NULL, produtos_pedidos_id INT DEFAULT NULL, placa_carro_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', forma_de_pagamento VARCHAR(255) NOT NULL, INDEX IDX_6716CCAA8A23699C (cpf_cliente_id), INDEX IDX_6716CCAAA3DB4739 (produtos_pedidos_id), INDEX IDX_6716CCAAA8A9C8B (placa_carro_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAA8A23699C FOREIGN KEY (cpf_cliente_id) REFERENCES cliente (id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAAA3DB4739 FOREIGN KEY (produtos_pedidos_id) REFERENCES produtos (id)');
        $this->addSql('ALTER TABLE pedidos ADD CONSTRAINT FK_6716CCAAA8A9C8B FOREIGN KEY (placa_carro_id) REFERENCES transporte (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAA8A23699C');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAAA3DB4739');
        $this->addSql('ALTER TABLE pedidos DROP FOREIGN KEY FK_6716CCAAA8A9C8B');
        $this->addSql('DROP TABLE pedidos');
    }
}
