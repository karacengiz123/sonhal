<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190702084533 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE evau');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE poll_logs');
        $this->addSql('DROP TABLE queue_log_salih');
        $this->addSql('DROP TABLE queues_members_ex');
        $this->addSql('DROP TABLE sessions_old');
        $this->addSql('ALTER TABLE realtime_queues CHANGE name name VARCHAR(128) NOT NULL');
        $this->addSql('ALTER TABLE cities CHANGE citystr citystr VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE config CHANGE name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE prefixes CHANGE prefix prefix CHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE ps_aors CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_asterisk_publications CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_auths CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_contacts CHANGE id id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ps_domain_aliases CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_endpoint_id_ips CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_endpoints CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_globals CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_inbound_publications CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_outbound_publishes CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_registrations CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_resource_list CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_subscription_persistence CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_systems CHANGE id id VARCHAR(40) NOT NULL');
        $this->addSql('ALTER TABLE ps_transports CHANGE id id VARCHAR(40) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE evau (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, username_canonical VARCHAR(180) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, email VARCHAR(180) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, email_canonical VARCHAR(180) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL COLLATE utf8_general_ci, password VARCHAR(255) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL COLLATE utf8_general_ci, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COLLATE utf8_general_ci COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE poll_logs (idx INT AUTO_INCREMENT NOT NULL, dt DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, call_id CHAR(80) DEFAULT NULL COLLATE utf8_general_ci, poll_id INT DEFAULT 0 NOT NULL, q_id INT DEFAULT 0 NOT NULL, answer CHAR(3) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, INDEX i_call_id (call_id), PRIMARY KEY(idx)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE queue_log_salih (id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, time VARCHAR(26) NOT NULL COLLATE utf8_general_ci, queuename VARCHAR(20) NOT NULL COLLATE utf8_general_ci, agent VARCHAR(20) NOT NULL COLLATE utf8_general_ci, event VARCHAR(20) NOT NULL COLLATE utf8_general_ci, data1 VARCHAR(100) NOT NULL COLLATE utf8_general_ci, data2 VARCHAR(100) NOT NULL COLLATE utf8_general_ci, data3 VARCHAR(100) NOT NULL COLLATE utf8_general_ci, data4 VARCHAR(100) NOT NULL COLLATE utf8_general_ci, data5 VARCHAR(100) NOT NULL COLLATE utf8_general_ci, created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, callid VARCHAR(40) NOT NULL COLLATE utf8_general_ci, INDEX event (event), INDEX queue (queuename), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE queues_members_ex (idx INT AUTO_INCREMENT NOT NULL, queue CHAR(9) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, member CHAR(10) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, penalty CHAR(1) DEFAULT \'\' NOT NULL COLLATE utf8_general_ci, UNIQUE INDEX quee_member (queue, member), PRIMARY KEY(idx)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sessions_old (sess_id VARCHAR(128) DEFAULT \'\' NOT NULL COLLATE utf8_bin, sess_data BLOB NOT NULL, sess_time INT UNSIGNED NOT NULL, sess_lifetime INT NOT NULL, id BIGINT UNSIGNED DEFAULT NULL, PRIMARY KEY(sess_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cities CHANGE citystr citystr VARCHAR(255) NOT NULL COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE config CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE extens DROP FOREIGN KEY FK_B86ACCEBA76ED395');
        $this->addSql('ALTER TABLE prefixes CHANGE prefix prefix CHAR(3) NOT NULL COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE ps_aors CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_asterisk_publications CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_auths CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_contacts CHANGE id id VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_domain_aliases CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_endpoint_id_ips CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_endpoints CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_globals CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_inbound_publications CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_outbound_publishes CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_registrations CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_resource_list CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_subscription_persistence CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_systems CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE ps_transports CHANGE id id VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE realtime_queues CHANGE name name VARCHAR(128) NOT NULL COLLATE utf8_general_ci');
    }
}
