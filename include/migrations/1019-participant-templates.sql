DROP TABLE IF EXISTS participantidtemplates;

CREATE TABLE participantidtemplates (
    template_id INT UNSIGNED NOT NULL,
    participant_id INT NOT NULL,
    PRIMARY KEY template_participant (template_id, participant_id),
    CONSTRAINT FOREIGN KEY participant (participant_id) REFERENCES deltagere (id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT FOREIGN KEY template (template_id) REFERENCES idtemplates (id) ON UPDATE CASCADE ON DELETE CASCADE
) engine=InnoDB character set utf8;
