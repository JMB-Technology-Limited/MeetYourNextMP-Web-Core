ALTER TABLE human_information ADD party VARCHAR(255);

ALTER TABLE human_history ADD party VARCHAR(255);
ALTER TABLE human_history ADD party_changed SMALLINT DEFAULT '0' NOT NULL;