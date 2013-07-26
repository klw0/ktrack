<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120422234106 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");
        
        //$this->addSql("ALTER TABLE trafficsource ALTER serviceauthenticationmethod SET ");
        //$this->addSql("ALTER TABLE trafficsource ALTER targetparameter SET ");
        $this->addSql("ALTER TABLE campaign ALTER creativeidentifiers TYPE TEXT");
        $this->addSql("ALTER TABLE offer ADD description TEXT DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql");
        
        $this->addSql("ALTER TABLE Offer DROP description");
        $this->addSql("ALTER TABLE Campaign ALTER creativeIdentifiers TYPE TEXT");
        //$this->addSql("ALTER TABLE TrafficSource ALTER targetParameter SET  DEFAULT ''target'::character varying'");
        //$this->addSql("ALTER TABLE TrafficSource ALTER serviceAuthenticationMethod SET  DEFAULT ''username_password'::character varying'");
    }
}