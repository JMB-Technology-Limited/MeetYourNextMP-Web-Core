CREATE TABLE organiser_email (
	id SERIAL,
	event_id INTEGER NOT NULL,
	email VARCHAR(255) NOT NULL,
	subject VARCHAR(255) NOT NULL,
	body_text TEXT NOT NULL,
	body_html TEXT NOT NULL,
	created_at timestamp without time zone NOT NULL,
	sent_at timestamp without time zone NULL,
	PRIMARY KEY(id)
);
ALTER TABLE organiser_email ADD CONSTRAINT organiser_email_event_id FOREIGN KEY (event_id) REFERENCES event_information(id);
