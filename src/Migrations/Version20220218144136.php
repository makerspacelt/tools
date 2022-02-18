<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220218144136 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO tools_users 
        (username, password, fullname, email, roles) 
        VALUES 
        (\'admin\', \'$2y$13$keVylPMpx98paRraE2tATOFRStZQPvDOg45K035R2NvTnVw8tqF7i\', \'Admin Adminiauskas\', \'admin@asdf.lt\', \'ROLE_SUPERADMIN\')');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
