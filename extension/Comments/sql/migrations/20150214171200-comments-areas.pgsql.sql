CREATE TABLE area_comment_information (
	id SERIAL NOT NULL,
	area_id INTEGER NOT NULL,
	slug INTEGER NOT NULL,
	title VARCHAR(255) NOT NULL,
	comment TEXT NULL,
	user_account_id INTEGER,
	is_deleted boolean default '0' NOT NULL,
	is_closed_by_admin boolean default '0' NOT NULL,
	created_at timestamp without time zone NOT NULL,
	approved_at timestamp without time zone NULL,
	PRIMARY KEY(id)
);
ALTER TABLE area_comment_information ADD CONSTRAINT area_comment_area_id FOREIGN KEY (area_id) REFERENCES area_information(id);
ALTER TABLE area_comment_information ADD CONSTRAINT area_comment_user_account_id FOREIGN KEY (user_account_id) REFERENCES user_account_information(id);
CREATE UNIQUE INDEX area_comment_information_slug ON area_comment_information(area_id, slug);


CREATE TABLE area_comment_history (
	area_comment_id INTEGER NOT NULL,
	title VARCHAR(255) NOT NULL,
	title_changed SMALLINT DEFAULT '0' NOT NULL,
	comment TEXT NULL,
	comment_changed SMALLINT DEFAULT '0' NOT NULL,
	is_deleted boolean NULL,
	is_deleted_changed SMALLINT DEFAULT '0' NOT NULL,
	is_closed_by_admin boolean NULL,
  is_closed_by_admin_changed SMALLINT DEFAULT '0' NOT NULL,
	user_account_id INTEGER,
	api2_application_id INTEGER NULL,
	is_new SMALLINT DEFAULT '0',
	created_at timestamp without time zone NOT NULL,
	approved_at timestamp without time zone NULL,
	PRIMARY KEY(area_comment_id,created_at)
);
ALTER TABLE area_comment_history ADD CONSTRAINT area_comment_history_area_comment_id FOREIGN KEY (area_comment_id) REFERENCES area_comment_information(id);
ALTER TABLE area_comment_history ADD CONSTRAINT area_comment_history_user_account_id FOREIGN KEY (user_account_id) REFERENCES user_account_information(id);
ALTER TABLE area_comment_history ADD CONSTRAINT area_comment_history_api2_application_id FOREIGN KEY (api2_application_id) REFERENCES api2_application_information(id);
