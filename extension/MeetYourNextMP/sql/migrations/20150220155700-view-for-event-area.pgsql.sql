CREATE VIEW meetyournextmp_event_in_area (event_id, area_id)
 AS


SELECT event_information.id  AS event_id, event_information.area_id AS area_id
FROM event_information
WHERE area_id IS NOT NULL
GROUP BY event_id, area_id

UNION

SELECT event_information.id  AS event_id, venue_information.area_id AS area_id
FROM event_information
JOIN venue_information ON event_information.venue_id = venue_information.id
WHERE venue_information.area_id IS NOT NULL
GROUP BY event_id, venue_information.area_id

UNION

SELECT event_information.id  AS event_id, cached_area_has_parent.has_parent_area_id AS area_id
FROM event_information
JOIN cached_area_has_parent ON event_information.area_id = cached_area_has_parent.area_id
WHERE event_information.area_id IS NOT NULL
GROUP BY event_id, cached_area_has_parent.has_parent_area_id

UNION

SELECT event_information.id  AS event_id, cached_area_has_parent.has_parent_area_id AS area_id
FROM event_information
JOIN venue_information ON event_information.venue_id = venue_information.id
JOIN cached_area_has_parent ON venue_information.area_id = cached_area_has_parent.area_id
WHERE venue_information.area_id IS NOT NULL
GROUP BY event_id, cached_area_has_parent.has_parent_area_id

UNION

SELECT event_information.id  AS event_id, human_in_area.area_id AS area_id
FROM event_information
JOIN event_has_human ON event_has_human.event_id = event_information.id AND event_has_human.removed_at IS NULL
JOIN human_in_area ON human_in_area.human_id = event_has_human.human_id AND human_in_area.removed_at IS NULL
GROUP BY event_information.id, human_in_area.area_id

UNION

SELECT event_information.id  AS event_id, cached_area_has_parent.has_parent_area_id AS area_id
FROM event_information
JOIN event_has_human ON event_has_human.event_id = event_information.id AND event_has_human.removed_at IS NULL
JOIN human_in_area ON human_in_area.human_id = event_has_human.human_id AND human_in_area.removed_at IS NULL
JOIN cached_area_has_parent ON  human_in_area.area_id = cached_area_has_parent.area_id
GROUP BY event_information.id, cached_area_has_parent.has_parent_area_id

;

