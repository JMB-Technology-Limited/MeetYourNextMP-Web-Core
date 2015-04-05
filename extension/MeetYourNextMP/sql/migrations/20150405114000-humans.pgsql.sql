ALTER TABLE human_information ADD url VARCHAR(255);

ALTER TABLE human_history ADD url VARCHAR(255);
ALTER TABLE human_history ADD url_changed SMALLINT DEFAULT '0' NOT NULL;