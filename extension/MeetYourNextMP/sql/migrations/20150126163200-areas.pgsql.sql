CREATE TABLE area_mapit_information (
	id SERIAL NOT NULL,
	area_id INTEGER NOT NULL,
	name VARCHAR (255) NULL,
	code_gss VARCHAR(100) NULL,
	code_unit_id VARCHAR(100) NULL,
	mapit_id VARCHAR(100) NULL,
	PRIMARY KEY(id)
);
ALTER TABLE area_mapit_information ADD CONSTRAINT area_mapit_information_area_id FOREIGN KEY (area_id) REFERENCES area_information(id);

CREATE INDEX area_mapit_information_mapit_id ON area_mapit_information(mapit_id);
CREATE INDEX area_mapit_information_code_gss ON area_mapit_information(code_gss);
