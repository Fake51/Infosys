UPDATE deltagere SET deltager_note = CONCAT('{"note" :"', deltager_note, '"}' );
ALTER TABLE deltagere MODIFY deltager_note JSON;