
 The following SQL statements will be executed:

     ALTER TABLE form_section CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE title title VARCHAR(255) NOT NULL;
     ALTER TABLE evaluation CHANGE status status INT DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL;
     ALTER TABLE ivr_service_log CHANGE creates_at creates_at DATETIME DEFAULT NULL;
     ALTER TABLE form_template DROP imported, CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE agent_break CHANGE end_time end_time DATETIME DEFAULT '0001-01-01 00:00:00';
     ALTER TABLE team CHANGE manager_backup_id manager_backup_id INT DEFAULT NULL;
     ALTER TABLE user CHANGE break_group_id break_group_id INT DEFAULT NULL, CHANGE team_id_id team_id_id INT DEFAULT NULL, CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL;
     ALTER TABLE acw_log CHANGE end_time end_time DATETIME DEFAULT NULL, CHANGE acw_type_id acw_type_id INT DEFAULT NULL;
     ALTER TABLE form_category CHANGE id id INT AUTO_INCREMENT NOT NULL;
     ALTER TABLE siebel_log ADD srid VARCHAR(255) DEFAULT NULL, ADD contact_id VARCHAR(255) DEFAULT NULL, CHANGE callid callid VARCHAR(60) DEFAULT NULL, CHANGE created_date created_date DATETIME DEFAULT NULL, CHANGE activity_id activity_id VARCHAR(255) DEFAULT NULL;
     ALTER TABLE guide CHANGE guide_group_id_id guide_group_id_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL;
     ALTER TABLE user_profile CHANGE user_id user_id INT DEFAULT NULL;
     ALTER TABLE report CHANGE firstdate firstdate DATETIME DEFAULT NULL, CHANGE lastdate lastdate DATETIME DEFAULT NULL;
     ALTER TABLE login_log ADD last_online DATETIME DEFAULT NULL, CHANGE start_time start_time DATETIME DEFAULT NULL, CHANGE end_time end_time DATETIME DEFAULT NULL;
     ALTER TABLE evaluation_answer CHANGE deleted_at deleted_at DATETIME DEFAULT NULL;
     ALTER TABLE trunks CHANGE username username VARCHAR(255) DEFAULT NULL, CHANGE secret secret VARCHAR(255) DEFAULT NULL, CHANGE ip ip VARCHAR(255) DEFAULT NULL, CHANGE did did VARCHAR(255) DEFAULT NULL, CHANGE tech tech VARCHAR(255) DEFAULT NULL, CHANGE channelid channelid VARCHAR(255) DEFAULT NULL;
     ALTER TABLE cdr CHANGE calldate calldate DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL, CHANGE userfield userfield VARCHAR(255) DEFAULT NULL, CHANGE did did CHAR(12) DEFAULT NULL, CHANGE scode scode CHAR(10) DEFAULT NULL, CHANGE src_org src_org VARCHAR(80) DEFAULT NULL, CHANGE dst_org dst_org VARCHAR(80) DEFAULT NULL, CHANGE call_id call_id VARCHAR(255) DEFAULT NULL;
     ALTER TABLE hosts CHANGE host host CHAR(6) DEFAULT 'halo00' NOT NULL, CHANGE out_ip out_ip VARCHAR(255) DEFAULT NULL;
     ALTER TABLE targets CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE img_src img_src VARCHAR(255) DEFAULT NULL, CHANGE table_name table_name VARCHAR(255) DEFAULT NULL, CHANGE target_name target_name VARCHAR(255) DEFAULT NULL, CHANGE target_value target_value VARCHAR(255) DEFAULT NULL, CHANGE target_str target_str VARCHAR(255) DEFAULT NULL;
     ALTER TABLE ivr_logs CHANGE dt dt DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL, CHANGE call_id call_id CHAR(80) DEFAULT NULL;
     ALTER TABLE voicemail CHANGE description description VARCHAR(255) DEFAULT NULL;
     ALTER TABLE time_conditions CHANGE description description VARCHAR(250) DEFAULT NULL;
     ALTER TABLE config CHANGE name name VARCHAR(255) NOT NULL, CHANGE value value VARCHAR(255) DEFAULT NULL;
     ALTER TABLE conf_rooms CHANGE description description VARCHAR(255) DEFAULT NULL;
     ALTER TABLE did CHANGE cus_id cus_id CHAR(7) DEFAULT '9340000' NOT NULL, CHANGE prefix prefix CHAR(10) DEFAULT '90' NOT NULL;
     ALTER TABLE inbound_routes CHANGE cus_id cus_id CHAR(7) DEFAULT '9340000' NOT NULL;
     ALTER TABLE disa CHANGE description description VARCHAR(255) DEFAULT NULL;
     ALTER TABLE prefixes CHANGE prefix prefix CHAR(3) NOT NULL;
     ALTER TABLE ivr_choices CHANGE choice choice CHAR(3) DEFAULT 'i' NOT NULL COMMENT '0-9, *,';
     ALTER TABLE cities CHANGE citystr citystr VARCHAR(255) NOT NULL;
     ALTER TABLE firewall CHANGE ast_id ast_id CHAR(10) DEFAULT 'dast00' NOT NULL, CHANGE cus_id cus_id INT DEFAULT NULL, CHANGE maskbits maskbits CHAR(5) DEFAULT '32' NOT NULL;
     ALTER TABLE queue_log CHANGE created created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL;
     ALTER TABLE custom_extens CHANGE description description VARCHAR(255) DEFAULT NULL;
     ALTER TABLE custom_dialplan CHANGE description description VARCHAR(255) DEFAULT NULL;
     ALTER TABLE extens CHANGE tech tech CHAR(6) DEFAULT 'SIP' NOT NULL, CHANGE cp_group cp_group CHAR(10) DEFAULT NULL;
     ALTER TABLE queues CHANGE strategy strategy CHAR(20) DEFAULT 'rrmemory' NOT NULL COMMENT 'ringall, leastrecent, fewestcalls, random, rrmemory, linear, wrandom';
