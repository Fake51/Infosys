ALTER TABLE deltagere DROP COLUMN scenarie;
ALTER TABLE deltagere ADD COLUMN game_id INT(11) DEFAULT NULL;
ALTER TABLE deltagere ADD CONSTRAINT deltagere_ibfk_5 FOREIGN KEY (game_id) REFERENCES aktiviteter(id);