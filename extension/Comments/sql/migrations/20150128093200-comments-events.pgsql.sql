CREATE TABLE event_comment_information (
	id SERIAL NOT NULL,
	event_id INTEGER NOT NULL,
	slug INTEGER NOT NULL,
	title VARCHAR(255) NOT NULL,
	comment TEXT NULL,
	user_account_id INTEGER,
	is_deleted boolean default '0' NOT NULL,
	created_at timestamp without time zone NOT NULL,
	approved_at timestamp without time zone NULL,
	PRIMARY KEY(id)
);
ALTER TABLE event_comment_information ADD CONSTRAINT event_comment_event_id FOREIGN KEY (event_id) REFERENCES event_information(id);
ALTER TABLE event_comment_information ADD CONSTRAINT event_comment_user_account_id FOREIGN KEY (user_account_id) REFERENCES user_account_information(id);
CREATE UNIQUE INDEX event_comment_information_slug ON event_comment_information(event_id, slug);


CREATE TABLE event_comment_history (
	event_comment_id INTEGER NOT NULL,
	title VARCHAR(255) NOT NULL,
	title_changed SMALLINT DEFAULT '0' NOT NULL,
	comment TEXT NULL,
	comment_changed SMALLINT DEFAULT '0' NOT NULL,
	is_deleted boolean NULL,
	is_deleted_changed SMALLINT DEFAULT '0' NOT NULL,
	user_account_id INTEGER,
	api2_application_id INTEGER NULL,
	is_new SMALLINT DEFAULT '0',
	created_at timestamp without time zone NOT NULL,
	approved_at timestamp without time zone NULL,
	PRIMARY KEY(event_comment_id,created_at)
);
ALTER TABLE event_comment_history ADD CONSTRAINT event_comment_history_event_comment_id FOREIGN KEY (event_comment_id) REFERENCES event_comment_information(id);
ALTER TABLE event_comment_history ADD CONSTRAINT event_comment_history_user_account_id FOREIGN KEY (user_account_id) REFERENCES user_account_information(id);
ALTER TABLE event_comment_history ADD CONSTRAINT event_comment_history_api2_application_id FOREIGN KEY (api2_application_id) REFERENCES api2_application_information(id);
