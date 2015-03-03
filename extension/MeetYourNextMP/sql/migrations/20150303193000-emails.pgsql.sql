CREATE TABLE human_email (
	id SERIAL,
	human_id INTEGER NOT NULL,
	email VARCHAR(255) NOT NULL,
	subject VARCHAR(255) NOT NULL,
	body_text TEXT NOT NULL,
	body_html TEXT NOT NULL,
	created_at timestamp without time zone NOT NULL,
	sent_at timestamp without time zone NULL,
	PRIMARY KEY(id)
);
ALTER TABLE human_email ADD CONSTRAINT human_email_human_id FOREIGN KEY (human_id) REFERENCES human_information(id);
