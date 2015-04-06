CREATE TABLE human_tweet (
	id SERIAL,
	human_id INTEGER NOT NULL,
	twitter_id BIGINT NULL,
	text VARCHAR(255) NOT NULL,
	created_at timestamp without time zone NOT NULL,
	sent_at timestamp without time zone NULL,
	PRIMARY KEY(id)
);
ALTER TABLE human_tweet ADD CONSTRAINT human_tweet_human_id FOREIGN KEY (human_id) REFERENCES human_information(id);
