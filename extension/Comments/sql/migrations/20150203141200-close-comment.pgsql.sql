ALTER TABLE event_comment_information ADD is_closed_by_admin boolean default '0' NOT NULL;

ALTER TABLE event_comment_history ADD is_closed_by_admin boolean NULL;
ALTER TABLE event_comment_history ADD is_closed_by_admin_changed SMALLINT DEFAULT '0' NOT NULL;
