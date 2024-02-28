CREATE TABLE hopin_invites (
  badge_no VARCHAR(5) PRIMARY KEY,
  invite_url VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (badge_no) REFERENCES members(badge_no)
);
