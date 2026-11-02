-- Data for tools/snapshot/capture so every page has something to show.
-- Load into a scratch database only.
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM role_permissions WHERE role_id IN (2, 1);
INSERT INTO role_permissions (role_id, permission_id)
  SELECT 2, permission_id FROM permissions WHERE name NOT LIKE '%-coming-soon' AND name NOT LIKE '%-redirect';
INSERT INTO role_permissions (role_id, permission_id)
  SELECT 1, permission_id FROM permissions WHERE name LIKE '%-coming-soon';

DELETE FROM members WHERE badge_no IN ('a001', 'm002', 'm003');
INSERT INTO members (badge_no, email, name, real_name) VALUES
  ('a001', 'admin@example.com', 'Admin <Person> & Co', 'Admin Real'),
  ('m002', 'member@example.com', 'Plain Member', NULL),
  ('m003', 'admin@example.com', 'Second O''Admin', NULL);
DELETE FROM member_roles;
INSERT INTO member_roles (badge_no, role_id) VALUES ('a001', 2), ('m003', 2), ('m002', 1), ('m002', 11);

DELETE FROM prog_sessions; DELETE FROM prog_stages; DELETE FROM prog_stream_urls;
DELETE FROM prog_replay; DELETE FROM prog_discord_posts; DELETE FROM signups; DELETE FROM prog_zoom;
INSERT INTO prog_sessions (item_id, destination, title, handler) VALUES ('100', 'https://example.com/s/100', 'Panel & <Talk>', 'stream'), ('101', 'https://example.com/s/101', 'Second panel', 'zoomevents');
INSERT INTO prog_stages (room_id, type, viewer_url, participant_url, title) VALUES ('r1', 'hybrid', 'https://example.com/v/r1', 'https://example.com/p/r1', 'Main Hall');
INSERT INTO prog_stream_urls (room_id, title, url) VALUES ('r1', 'Main Hall', 'https://example.com/stream/r1');
INSERT INTO prog_replay (item_id, replay_url, title, unavailable) VALUES ('100', 'https://example.com/replay/100', 'Panel & <Talk>', 0), ('101', NULL, 'Gone', 1);
INSERT INTO prog_discord_posts (item_id, start, duration, room_id, post_url, title) VALUES ('100', 1735725600, 60, 'r1', 'https://discord.com/channels/1/2/3', 'Panel & <Talk>');
INSERT INTO signups (id, title, category, url, guide_url, notes, session_start, opens_at, closes_at) VALUES
  (1, 'Kaffeeklatsch & chat', 'Kaffeeklatsch', 'https://example.com/signup/1', 'https://example.com/guide/1', 'Bring <cake>', 1735729200, 1704067200, 1893456000),
  (2, 'Closed one', 'Workshop', 'https://example.com/signup/2', NULL, NULL, 1735815600, 1893456000, 1896134400);
INSERT INTO prog_zoom (zoom_url) VALUES ('https://example.com/zoom');

DELETE FROM business_meeting_links;
INSERT INTO business_meeting_links (badge_no, join_link, created_at, updated_at) VALUES ('a001', 'https://example.com/join/a001?x=1&y=2', 1733011200, 1733011200);

DELETE FROM site_selection_tokens; DELETE FROM site_selection_info; DELETE FROM site_selection_payment_links;
INSERT INTO site_selection_info (badge_no, country, address, wants_paper_publications, consent_to_publish_name, created_at, updated_at) VALUES ('a001', 'GB', '1 Example <Street>\nTown', 1, 1, 1733011200, 1733011200);
INSERT INTO site_selection_tokens (id, token, label, badge_no, method, sold_by, sold_at, spoilt_at, added_by, added_at) VALUES
  (1, 'TOKEN-AAA', 'Batch & 1', 'a001', 'online', NULL, 1735722000, NULL, 'a001', 1733011200),
  (2, 'TOKEN-BBB', 'Batch & 1', NULL, NULL, NULL, NULL, NULL, 'a001', 1733011200),
  (3, 'TOKEN-CCC', 'Batch & 1', 'm002', 'desk', 'a001', 1735723800, 1735725600, 'a001', 1733011200);

DELETE FROM discord_ids;
INSERT INTO discord_ids (badge_no, discord_id, username) VALUES ('a001', '123456', 'admin<user>');
DELETE FROM conclar_selections;
INSERT INTO conclar_selections (badge_no, app_id, item_id, selected) VALUES ('a001', 'app1', '100', 1), ('m002', 'app1', '100', 1);
SET FOREIGN_KEY_CHECKS = 1;
DELETE FROM sessions;
DELETE FROM login_attempts;
