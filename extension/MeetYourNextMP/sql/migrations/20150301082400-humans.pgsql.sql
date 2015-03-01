ALTER TABLE human_information ADD email VARCHAR(255);
ALTER TABLE human_information ADD twitter VARCHAR(255);
ALTER TABLE human_information ADD image_url TEXT;

ALTER TABLE human_history ADD email VARCHAR(255);
ALTER TABLE human_history ADD email_changed SMALLINT DEFAULT '0' NOT NULL;
ALTER TABLE human_history ADD twitter VARCHAR(255);
ALTER TABLE human_history ADD twitter_changed SMALLINT DEFAULT '0' NOT NULL;
ALTER TABLE human_history ADD image_url TEXT;
ALTER TABLE human_history ADD image_url_changed SMALLINT DEFAULT '0' NOT NULL;

