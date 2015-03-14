ALTER TABLE human_information ADD is_duplicate_of_id INTEGER NULL;
ALTER TABLE human_information ADD CONSTRAINT human_information_is_duplicate_of_id FOREIGN KEY (is_duplicate_of_id) REFERENCES human_information(id);
ALTER TABLE human_history ADD is_duplicate_of_id INTEGER NULL;
ALTER TABLE human_history ADD   is_duplicate_of_id_changed SMALLINT DEFAULT '0' NOT NULL;
UPDATE human_history SET is_duplicate_of_id_changed = -1;
