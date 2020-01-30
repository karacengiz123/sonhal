<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190508075357 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE chat_message (id INT AUTO_INCREMENT NOT NULL, chat_id VARCHAR(20) NOT NULL, sender SMALLINT NOT NULL, message LONGTEXT NOT NULL, INDEX IDX_FAB3FC161A9A7125 (chat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_FAB3FC161A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id)');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE parameters');
        $this->addSql('DROP TABLE sessions');
        $this->addSql('ALTER TABLE acw_log CHANGE end_time end_time DATETIME DEFAULT NULL, CHANGE acw_type_id acw_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE guide CHANGE guide_group_id_id guide_group_id_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE team CHANGE manager_backup_id manager_backup_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE break_group_id break_group_id INT DEFAULT NULL, CHANGE team_id_id team_id_id INT DEFAULT NULL, CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE evaluation CHANGE status status INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE ivr_service_log CHANGE creates_at creates_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE agent_break CHANGE end_time end_time DATETIME DEFAULT \'0001-01-01 00:00:00\'');
        $this->addSql('ALTER TABLE chat CHANGE id id VARCHAR(20) NOT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE end_time end_time DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE siebel_log CHANGE callid callid VARCHAR(60) DEFAULT NULL, CHANGE created_date created_date DATETIME DEFAULT NULL, CHANGE activity_id activity_id VARCHAR(255) DEFAULT NULL, CHANGE srid srid VARCHAR(255) DEFAULT NULL, CHANGE contact_id contact_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_chat_messages DROP FOREIGN KEY FK_755DF40A1A9A7125');
        $this->addSql('ALTER TABLE app_chat_messages CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE chat_id chat_id INT NOT NULL');
        $this->addSql('ALTER TABLE app_chat_messages ADD CONSTRAINT FK_755DF40A1A9A7125 FOREIGN KEY (chat_id) REFERENCES acw_log (id)');
        $this->addSql('ALTER TABLE user_profile CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report CHANGE firstdate firstdate DATETIME DEFAULT NULL, CHANGE lastdate lastdate DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE login_log CHANGE start_time start_time DATETIME DEFAULT NULL, CHANGE end_time end_time DATETIME DEFAULT NULL, CHANGE last_online last_online DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE evaluation_answer CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE cdr CHANGE calldate calldate DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, CHANGE userfield userfield VARCHAR(255) DEFAULT NULL, CHANGE did did CHAR(12) DEFAULT NULL, CHANGE scode scode CHAR(10) DEFAULT NULL, CHANGE src_org src_org VARCHAR(80) DEFAULT NULL, CHANGE dst_org dst_org VARCHAR(80) DEFAULT NULL, CHANGE call_id call_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE cities CHANGE citystr citystr VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE conf_rooms CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE config CHANGE name name VARCHAR(255) NOT NULL, CHANGE value value VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE custom_dialplan CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE custom_extens CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE did CHANGE cus_id cus_id CHAR(7) DEFAULT \'9340000\' NOT NULL, CHANGE prefix prefix CHAR(10) DEFAULT \'90\' NOT NULL');
        $this->addSql('ALTER TABLE disa CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE extens CHANGE tech tech CHAR(6) DEFAULT \'SIP\' NOT NULL, CHANGE cp_group cp_group CHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE firewall CHANGE ast_id ast_id CHAR(10) DEFAULT \'dast00\' NOT NULL, CHANGE cus_id cus_id INT DEFAULT NULL, CHANGE maskbits maskbits CHAR(5) DEFAULT \'32\' NOT NULL');
        $this->addSql('ALTER TABLE hosts CHANGE host host CHAR(6) DEFAULT \'halo00\' NOT NULL, CHANGE out_ip out_ip VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE inbound_routes CHANGE cus_id cus_id CHAR(7) DEFAULT \'9340000\' NOT NULL');
        $this->addSql('ALTER TABLE ivr_choices CHANGE choice choice CHAR(3) DEFAULT \'i\' NOT NULL COMMENT \'0-9, *,\'');
        $this->addSql('ALTER TABLE ivr_logs CHANGE dt dt DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, CHANGE call_id call_id CHAR(80) DEFAULT NULL');
        $this->addSql('ALTER TABLE prefixes CHANGE prefix prefix CHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE queue_log CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE queues CHANGE strategy strategy CHAR(20) DEFAULT \'rrmemory\' NOT NULL COMMENT \'ringall, leastrecent, fewestcalls, random, rrmemory, linear, wrandom\'');
        $this->addSql('ALTER TABLE targets CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE img_src img_src VARCHAR(255) DEFAULT NULL, CHANGE table_name table_name VARCHAR(255) DEFAULT NULL, CHANGE target_name target_name VARCHAR(255) DEFAULT NULL, CHANGE target_value target_value VARCHAR(255) DEFAULT NULL, CHANGE target_str target_str VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE time_conditions CHANGE description description VARCHAR(250) DEFAULT NULL');
        $this->addSql('ALTER TABLE trunks CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE secret secret VARCHAR(255) DEFAULT NULL, CHANGE ip ip VARCHAR(255) DEFAULT NULL, CHANGE did did VARCHAR(255) DEFAULT NULL, CHANGE tech tech VARCHAR(255) DEFAULT NULL, CHANGE channelid channelid VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE voicemail CHANGE description description VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, username_canonical VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, email VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, email_canonical VARCHAR(180) NOT NULL COLLATE utf8mb4_unicode_ci, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, password VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, last_login DATETIME DEFAULT \'NULL\', confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, password_requested_at DATETIME DEFAULT \'NULL\', roles LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE parameters (id INT AUTO_INCREMENT NOT NULL, param_key VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, param_value VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sessions (sess_id VARCHAR(128) NOT NULL COLLATE utf8_bin, sess_data BLOB NOT NULL, sess_time INT UNSIGNED NOT NULL, sess_lifetime INT NOT NULL, PRIMARY KEY(sess_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE chat_message');
        $this->addSql('ALTER TABLE acw_log CHANGE end_time end_time DATETIME DEFAULT \'NULL\', CHANGE acw_type_id acw_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE agent_break CHANGE end_time end_time DATETIME DEFAULT \'\'0001-01-01 00:00:00\'\'');
        $this->addSql('ALTER TABLE app_chat_messages DROP FOREIGN KEY FK_755DF40A1A9A7125');
        $this->addSql('ALTER TABLE app_chat_messages CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE chat_id chat_id VARCHAR(20) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE app_chat_messages ADD CONSTRAINT FK_755DF40A1A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id)');
        $this->addSql('ALTER TABLE cdr CHANGE calldate calldate DATETIME DEFAULT \'\'0000-00-00 00:00:00\'\' NOT NULL, CHANGE userfield userfield VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE did did CHAR(12) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE scode scode CHAR(10) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE src_org src_org VARCHAR(80) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE dst_org dst_org VARCHAR(80) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE call_id call_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE chat CHANGE id id VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE user_id user_id INT DEFAULT NULL, CHANGE end_time end_time DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE cities CHANGE citystr citystr VARCHAR(255) NOT NULL COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE conf_rooms CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE latin5_turkish_ci');
        $this->addSql('ALTER TABLE config CHANGE name name VARCHAR(255) NOT NULL COLLATE utf8_turkish_ci, CHANGE value value VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE custom_dialplan CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE custom_extens CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE did CHANGE cus_id cus_id CHAR(7) DEFAULT \'\'9340000\'\' NOT NULL COLLATE utf8_turkish_ci, CHANGE prefix prefix CHAR(10) DEFAULT \'\'90\'\' NOT NULL COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE disa CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE evaluation CHANGE status status INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE evaluation_answer CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE extens CHANGE tech tech CHAR(6) DEFAULT \'\'SIP\'\' NOT NULL COLLATE utf8_turkish_ci, CHANGE cp_group cp_group CHAR(10) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE firewall CHANGE ast_id ast_id CHAR(10) DEFAULT \'\'dast00\'\' NOT NULL COLLATE utf8_turkish_ci, CHANGE cus_id cus_id INT DEFAULT NULL, CHANGE maskbits maskbits CHAR(5) DEFAULT \'\'32\'\' NOT NULL COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE guide CHANGE guide_group_id_id guide_group_id_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE hosts CHANGE host host CHAR(6) DEFAULT \'\'halo00\'\' NOT NULL COLLATE utf8_turkish_ci, CHANGE out_ip out_ip VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE inbound_routes CHANGE cus_id cus_id CHAR(7) DEFAULT \'\'9340000\'\' NOT NULL COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE ivr_choices CHANGE choice choice CHAR(3) DEFAULT \'\'i\'\' NOT NULL COLLATE utf8_turkish_ci COMMENT \'0-9, *,\'');
        $this->addSql('ALTER TABLE ivr_logs CHANGE dt dt DATETIME DEFAULT \'\'0000-00-00 00:00:00\'\' NOT NULL, CHANGE call_id call_id CHAR(80) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE ivr_service_log CHANGE creates_at creates_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE login_log CHANGE start_time start_time DATETIME DEFAULT \'NULL\', CHANGE end_time end_time DATETIME DEFAULT \'NULL\', CHANGE last_online last_online DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE prefixes CHANGE prefix prefix CHAR(3) NOT NULL COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE queue_log CHANGE created created DATETIME DEFAULT \'current_timestamp()\' NOT NULL');
        $this->addSql('ALTER TABLE queues CHANGE strategy strategy CHAR(20) DEFAULT \'\'rrmemory\'\' NOT NULL COLLATE utf8_turkish_ci COMMENT \'ringall, leastrecent, fewestcalls, random, rrmemory, linear, wrandom\'');
        $this->addSql('ALTER TABLE report CHANGE firstdate firstdate DATETIME DEFAULT \'NULL\', CHANGE lastdate lastdate DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE siebel_log CHANGE callid callid VARCHAR(60) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE created_date created_date DATETIME DEFAULT \'NULL\', CHANGE activity_id activity_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE srid srid VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE contact_id contact_id VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE targets CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE img_src img_src VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE table_name table_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE target_name target_name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE target_value target_value VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE target_str target_str VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE team CHANGE manager_backup_id manager_backup_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE time_conditions CHANGE description description VARCHAR(250) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE trunks CHANGE username username VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE secret secret VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE ip ip VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE did did VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE tech tech VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci, CHANGE channelid channelid VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
        $this->addSql('ALTER TABLE user CHANGE break_group_id break_group_id INT DEFAULT NULL, CHANGE team_id_id team_id_id INT DEFAULT NULL, CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user_profile CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE voicemail CHANGE description description VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_turkish_ci');
    }
}
