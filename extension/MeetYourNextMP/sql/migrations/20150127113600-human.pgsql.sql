CREATE TABLE human_information (
	id SERIAL,
	site_id INTEGER NOT NULL,
	slug INTEGER NOT NULL,
	title VARCHAR(255) NOT NULL,
	description TEXT NULL,
	is_deleted boolean default '0' NOT NULL,
	created_at timestamp without time zone NOT NULL,
	approved_at timestamp without time zone NULL,
	PRIMARY KEY(id)
);
ALTER TABLE human_information ADD CONSTRAINT human_information_site_id FOREIGN KEY (site_id) REFERENCES site_information(id);
CREATE UNIQUE INDEX human_information_slug ON human_information(site_id, slug);

CREATE TABLE human_history (
	human_id INTEGER NOT NULL,
	title VARCHAR(255) NOT NULL,
	title_changed SMALLINT DEFAULT '0' NOT NULL,
	description TEXT NULL,
	description_changed SMALLINT DEFAULT '0' NOT NULL,
	is_deleted boolean NULL,
	is_deleted_changed SMALLINT DEFAULT '0' NOT NULL,
	user_account_id INTEGER,
	api2_application_id INTEGER NULL,
	is_new SMALLINT DEFAULT '0',
	created_at timestamp without time zone NOT NULL,
	approved_at timestamp without time zone NULL,
	PRIMARY KEY(human_id,created_at)
);
ALTER TABLE human_history ADD CONSTRAINT human_history_human_id FOREIGN KEY (human_id) REFERENCES human_information(id);
ALTER TABLE human_history ADD CONSTRAINT human_history_user_account_id FOREIGN KEY (user_account_id) REFERENCES user_account_information(id);
ALTER TABLE human_history ADD CONSTRAINT human_history_api2_application_id FOREIGN KEY (api2_application_id) REFERENCES api2_application_information(id);

CREATE TABLE event_has_human (
	human_id INTEGER NOT NULL,
	event_id INTEGER NOT NULL,
	added_by_user_account_id INTEGER NULL,
	added_at timestamp without time zone NOT NULL,
	addition_approved_at timestamp without time zone NULL,
	added_by_api2_application_id INTEGER NULL,
	removed_by_user_account_id INTEGER NULL,
	removed_at timestamp without time zone NULL,
	removal_approved_at timestamp without time zone NULL,
	removed_by_api2_application_id INTEGER NULL,
	PRIMARY KEY(human_id,event_id,added_at)
);
ALTER TABLE event_has_human ADD CONSTRAINT event_has_human_human_id FOREIGN KEY (human_id) REFERENCES human_information(id);
ALTER TABLE event_has_human ADD CONSTRAINT event_has_human_event_id FOREIGN KEY (event_id) REFERENCES event_information(id);
ALTER TABLE event_has_human ADD CONSTRAINT event_has_human_added_by_user_account_id FOREIGN KEY (added_by_user_account_id) REFERENCES user_account_information(id);
ALTER TABLE event_has_human ADD CONSTRAINT event_has_human_removed_by_user_account_id FOREIGN KEY (removed_by_user_account_id) REFERENCES user_account_information(id);
ALTER TABLE event_has_human ADD CONSTRAINT event_has_human_added_by_api2_application_id FOREIGN KEY (added_by_api2_application_id) REFERENCES api2_application_information(id);
ALTER TABLE event_has_human ADD CONSTRAINT event_has_human_removed_by_api2_application_id FOREIGN KEY (removed_by_api2_application_id) REFERENCES api2_application_information(id);

