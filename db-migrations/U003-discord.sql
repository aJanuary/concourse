CREATE TABLE discord_ids (
  badge_no VARCHAR(5) NOT NULL,
  discord_id VARCHAR(100) NOT NULL,
  username VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (badge_no, discord_id),
  FOREIGN KEY (badge_no) REFERENCES members(badge_no)
);