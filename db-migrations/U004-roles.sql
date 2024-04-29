CREATE TABLE roles (
  role_id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL
);

CREATE TABLE member_roles (
  badge_no VARCHAR(5) NOT NULL,
  role_id  bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (badge_no, role_id),
  FOREIGN KEY (badge_no) REFERENCES members(badge_no),
  FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

CREATE TABLE role_permissions (
  role_id  bigint(20) UNSIGNED NOT NULL,
  permission VARCHAR(255) NOT NULL,
  PRIMARY KEY (role_id, permission),
  FOREIGN KEY (role_id) REFERENCES roles(role_id)
);

INSERT INTO roles(name) VALUES ('default'), ('admin'), ('moderator'), ('programme participant'), ('programme moderator'), ('artist'), ('fan table'), ('dealer');
INSERT INTO roles(name) VALUES ('admin');
INSERT INTO roles(name) VALUES ('moderator');
INSERT INTO role_permissions(role_id, permission) SELECT role_id, 'manage-discord-ids' FROM roles WHERE name = 'moderator';
INSERT INTO role_permissions(role_id, permission) SELECT role_id, 'see-participant-guides' FROM roles WHERE name IN ('programme participant', 'programme moderator');