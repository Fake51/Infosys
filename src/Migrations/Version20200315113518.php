<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200315113518 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE board_game_event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, board_game_id INTEGER NOT NULL, type VARCHAR(64) NOT NULL, timestamp DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_A593A794AC91F10A ON board_game_event (board_game_id)');
        $this->addSql('CREATE TABLE participant (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, country_id INTEGER NOT NULL, name CLOB NOT NULL, gender VARCHAR(255) NOT NULL, email VARCHAR(380) NOT NULL, phone VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, postalcode VARCHAR(16) NOT NULL, city VARCHAR(255) NOT NULL, messaging BOOLEAN NOT NULL, super_gamemaster BOOLEAN NOT NULL, work_areas CLOB NOT NULL --(DC2Type:array)
        , password VARCHAR(255) NOT NULL, wanted_number_of_activities INTEGER NOT NULL, birth_date DATE NOT NULL --(DC2Type:date_immutable)
        , extra_vouchers INTEGER NOT NULL, google_cloud_messaging_id VARCHAR(255) NOT NULL, offered_skills CLOB NOT NULL, apple_push_id VARCHAR(255) NOT NULL, wanted_number_of_tasks INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D79F6B11F92F3E70 ON participant (country_id)');
        $this->addSql('CREATE TABLE participant_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(64) NOT NULL)');
        $this->addSql('CREATE TABLE participant_type_participant (participant_type_id INTEGER NOT NULL, participant_id INTEGER NOT NULL, PRIMARY KEY(participant_type_id, participant_id))');
        $this->addSql('CREATE INDEX IDX_59F21FE65F41439B ON participant_type_participant (participant_type_id)');
        $this->addSql('CREATE INDEX IDX_59F21FE69D1C3019 ON participant_type_participant (participant_id)');
        $this->addSql('CREATE TABLE country (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE schedule (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, activity_id INTEGER NOT NULL, meeting_location_id INTEGER NOT NULL, parent_schedule_id INTEGER DEFAULT NULL, start DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , "end" DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_5A3811FB81C06096 ON schedule (activity_id)');
        $this->addSql('CREATE INDEX IDX_5A3811FB2A2D2FFB ON schedule (meeting_location_id)');
        $this->addSql('CREATE INDEX IDX_5A3811FBCF27D0A5 ON schedule (parent_schedule_id)');
        $this->addSql('CREATE TABLE language (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(64) NOT NULL)');
        $this->addSql('CREATE TABLE language_participant (language_id INTEGER NOT NULL, participant_id INTEGER NOT NULL, PRIMARY KEY(language_id, participant_id))');
        $this->addSql('CREATE INDEX IDX_B4D91EA382F1BAF4 ON language_participant (language_id)');
        $this->addSql('CREATE INDEX IDX_B4D91EA39D1C3019 ON language_participant (participant_id)');
        $this->addSql('CREATE TABLE note (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, participant_id INTEGER NOT NULL, type VARCHAR(32) NOT NULL, content CLOB NOT NULL)');
        $this->addSql('CREATE INDEX IDX_CFBDFA149D1C3019 ON note (participant_id)');
        $this->addSql('CREATE TABLE participant_event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type VARCHAR(64) NOT NULL, timestamp DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , description VARCHAR(1024) NOT NULL, participant_id INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE activity_type (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(64) NOT NULL)');
        $this->addSql('CREATE TABLE activity (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type_id INTEGER NOT NULL, languages_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, duration DOUBLE PRECISION NOT NULL, minimum_group_participants INTEGER NOT NULL, maximum_group_participants INTEGER NOT NULL, number_of_group_hosts_required INTEGER NOT NULL, note CLOB NOT NULL, price NUMERIC(10, 2) NOT NULL, is_area_exclusive BOOLEAN NOT NULL, is_schedule_exclusive BOOLEAN NOT NULL, can_participate_more_than_once BOOLEAN NOT NULL, maximum_signups INTEGER NOT NULL, maximum_signups_per_schedule INTEGER NOT NULL, karma_type INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_AC74095AC54C8C93 ON activity (type_id)');
        $this->addSql('CREATE INDEX IDX_AC74095A5D237A9A ON activity (languages_id)');
        $this->addSql('CREATE TABLE activity_participant (activity_id INTEGER NOT NULL, participant_id INTEGER NOT NULL, PRIMARY KEY(activity_id, participant_id))');
        $this->addSql('CREATE INDEX IDX_D911011D81C06096 ON activity_participant (activity_id)');
        $this->addSql('CREATE INDEX IDX_D911011D9D1C3019 ON activity_participant (participant_id)');
        $this->addSql('CREATE TABLE area (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(128) NOT NULL, description VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, status INTEGER NOT NULL, password_reset_hash VARCHAR(255) NOT NULL, password_reset_time DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE TABLE location (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, area_id INTEGER NOT NULL, description VARCHAR(255) NOT NULL, name VARCHAR(128) NOT NULL, is_bookable BOOLEAN NOT NULL, bed_capacity INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_5E9E89CBBD0F409C ON location (area_id)');
        $this->addSql('CREATE TABLE board_game (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, owner VARCHAR(255) NOT NULL, comment CLOB NOT NULL, boardgamegeek_id INTEGER NOT NULL, is_origin_convention BOOLEAN NOT NULL)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE board_game_event');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE participant_type');
        $this->addSql('DROP TABLE participant_type_participant');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE schedule');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE language_participant');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE participant_event');
        $this->addSql('DROP TABLE activity_type');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_participant');
        $this->addSql('DROP TABLE area');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE board_game');
    }
}
