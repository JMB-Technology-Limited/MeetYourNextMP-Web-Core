
CREATE TABLE human_in_area (
	human_id INTEGER NOT NULL,
	area_id INTEGER NOT NULL,
	added_by_user_account_id INTEGER NULL,
	added_at timestamp without time zone NOT NULL,
	addition_approved_at timestamp without time zone NULL,
	added_by_api2_application_id INTEGER NULL,
	removed_by_user_account_id INTEGER NULL,
	removed_at timestamp without time zone NULL,
	removal_approved_at timestamp without time zone NULL,
	removed_by_api2_application_id INTEGER NULL,
	PRIMARY KEY(human_id,area_id,added_at)
);
ALTER TABLE human_in_area ADD CONSTRAINT human_in_area_human_id FOREIGN KEY (human_id) REFERENCES human_information(id);
ALTER TABLE human_in_area ADD CONSTRAINT human_in_area_area_id FOREIGN KEY (area_id) REFERENCES area_information(id);
ALTER TABLE human_in_area ADD CONSTRAINT human_in_area_added_by_user_account_id FOREIGN KEY (added_by_user_account_id) REFERENCES user_account_information(id);
ALTER TABLE human_in_area ADD CONSTRAINT human_in_area_removed_by_user_account_id FOREIGN KEY (removed_by_user_account_id) REFERENCES user_account_information(id);
ALTER TABLE human_in_area ADD CONSTRAINT human_in_area_added_by_api2_application_id FOREIGN KEY (added_by_api2_application_id) REFERENCES api2_application_information(id);
ALTER TABLE human_in_area ADD CONSTRAINT human_in_area_removed_by_api2_application_id FOREIGN KEY (removed_by_api2_application_id) REFERENCES api2_application_information(id);

