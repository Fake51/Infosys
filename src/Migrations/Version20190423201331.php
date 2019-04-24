<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190423201331 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, languages_id INT NOT NULL, name VARCHAR(255) NOT NULL, duration DOUBLE PRECISION NOT NULL, minimum_group_participants INT NOT NULL, maximum_group_participants INT NOT NULL, number_of_group_hosts_required INT NOT NULL, note LONGTEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, is_area_exclusive TINYINT(1) NOT NULL, is_schedule_exclusive TINYINT(1) NOT NULL, can_participate_more_than_once TINYINT(1) NOT NULL, maximum_signups INT NOT NULL, maximum_signups_per_schedule INT NOT NULL, karma_type INT NOT NULL, INDEX IDX_AC74095AC54C8C93 (type_id), INDEX IDX_AC74095A5D237A9A (languages_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_participant (activity_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_D911011D81C06096 (activity_id), INDEX IDX_D911011D9D1C3019 (participant_id), PRIMARY KEY(activity_id, participant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, participant_id INT NOT NULL, type VARCHAR(32) NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_CFBDFA149D1C3019 (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE language (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE board_game (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, owner VARCHAR(255) NOT NULL, comment LONGTEXT NOT NULL, boardgamegeek_id INT NOT NULL, is_origin_convention TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant_event (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(64) NOT NULL, timestamp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', description VARCHAR(1024) NOT NULL, participant_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant_type_participant (participant_type_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_59F21FE65F41439B (participant_type_id), INDEX IDX_59F21FE69D1C3019 (participant_id), PRIMARY KEY(participant_type_id, participant_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, status INT NOT NULL, password_reset_hash VARCHAR(255) NOT NULL, password_reset_time DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, languages_id INT NOT NULL, name LONGTEXT NOT NULL, gender VARCHAR(255) NOT NULL, email VARCHAR(380) NOT NULL, phone VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, postalcode VARCHAR(16) NOT NULL, city VARCHAR(255) NOT NULL, messaging TINYINT(1) NOT NULL, super_gamemaster TINYINT(1) NOT NULL, work_areas LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', password VARCHAR(255) NOT NULL, wanted_number_of_activities INT NOT NULL, birth_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', extra_vouchers INT NOT NULL, google_cloud_messaging_id VARCHAR(255) NOT NULL, offered_skills LONGTEXT NOT NULL, apple_push_id VARCHAR(255) NOT NULL, wanted_number_of_tasks INT NOT NULL, INDEX IDX_D79F6B11F92F3E70 (country_id), INDEX IDX_D79F6B115D237A9A (languages_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, area_id INT NOT NULL, description VARCHAR(255) NOT NULL, name VARCHAR(128) NOT NULL, is_bookable TINYINT(1) NOT NULL, bed_capacity INT NOT NULL, INDEX IDX_5E9E89CBBD0F409C (area_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE board_game_event (id INT AUTO_INCREMENT NOT NULL, board_game_id INT NOT NULL, type VARCHAR(64) NOT NULL, timestamp DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A593A794AC91F10A (board_game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE area (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE schedule (id INT AUTO_INCREMENT NOT NULL, activity_id INT NOT NULL, meeting_location_id INT NOT NULL, parent_schedule_id INT DEFAULT NULL, start DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5A3811FB81C06096 (activity_id), INDEX IDX_5A3811FB2A2D2FFB (meeting_location_id), INDEX IDX_5A3811FBCF27D0A5 (parent_schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AC54C8C93 FOREIGN KEY (type_id) REFERENCES activity_type (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A5D237A9A FOREIGN KEY (languages_id) REFERENCES language (id)');
        $this->addSql('ALTER TABLE activity_participant ADD CONSTRAINT FK_D911011D81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_participant ADD CONSTRAINT FK_D911011D9D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA149D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
        $this->addSql('ALTER TABLE participant_type_participant ADD CONSTRAINT FK_59F21FE65F41439B FOREIGN KEY (participant_type_id) REFERENCES participant_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant_type_participant ADD CONSTRAINT FK_59F21FE69D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B115D237A9A FOREIGN KEY (languages_id) REFERENCES language (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBBD0F409C FOREIGN KEY (area_id) REFERENCES area (id)');
        $this->addSql('ALTER TABLE board_game_event ADD CONSTRAINT FK_A593A794AC91F10A FOREIGN KEY (board_game_id) REFERENCES board_game (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FB2A2D2FFB FOREIGN KEY (meeting_location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE schedule ADD CONSTRAINT FK_5A3811FBCF27D0A5 FOREIGN KEY (parent_schedule_id) REFERENCES schedule (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activity_participant DROP FOREIGN KEY FK_D911011D81C06096');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB81C06096');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A5D237A9A');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B115D237A9A');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11F92F3E70');
        $this->addSql('ALTER TABLE board_game_event DROP FOREIGN KEY FK_A593A794AC91F10A');
        $this->addSql('ALTER TABLE participant_type_participant DROP FOREIGN KEY FK_59F21FE65F41439B');
        $this->addSql('ALTER TABLE activity_participant DROP FOREIGN KEY FK_D911011D9D1C3019');
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA149D1C3019');
        $this->addSql('ALTER TABLE participant_type_participant DROP FOREIGN KEY FK_59F21FE69D1C3019');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AC54C8C93');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FB2A2D2FFB');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CBBD0F409C');
        $this->addSql('ALTER TABLE schedule DROP FOREIGN KEY FK_5A3811FBCF27D0A5');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_participant');
        $this->addSql('DROP TABLE note');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE board_game');
        $this->addSql('DROP TABLE participant_event');
        $this->addSql('DROP TABLE participant_type');
        $this->addSql('DROP TABLE participant_type_participant');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE activity_type');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE board_game_event');
        $this->addSql('DROP TABLE area');
        $this->addSql('DROP TABLE schedule');
    }
}
