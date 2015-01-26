CREATE TABLE human_popit_information (
	id SERIAL NOT NULL,
	human_id INTEGER NOT NULL,
	popit_id INTEGER NOT NULL,
	mapit_id INTEGER NULL,
	name VARCHAR (255) NULL,
	PRIMARY KEY(id)
);
ALTER TABLE human_popit_information ADD CONSTRAINT human_mapit_information_human_id FOREIGN KEY (human_id) REFERENCES human_information(id);

CREATE INDEX human_popit_information_popit_id ON human_popit_information(popit_id);
