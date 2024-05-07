-- Clean up the duplicate admin and moderator roles
ALTER TABLE role_permissions DROP CONSTRAINT role_permissions_ibfk_1;
ALTER TABLE role_permissions ADD CONSTRAINT FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE;

DELETE FROM
  roles
USING
  roles,
  roles roles2
WHERE
  roles.role_id > roles2.role_id
  AND roles.name = roles2.name
  AND roles.name IN ('admin', 'moderator');

-- Create permission table structure
CREATE TABLE permissions (
  permission_id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL
);

CREATE UNIQUE INDEX permissions_name_uk ON permissions(name);

INSERT INTO permissions(name) VALUES
  ('see-readme'),
  ('see-guide'),
  ('see-discord'),
  ('see-newsletter'),
  ('see-souvenir'),
  ('see-vote'),
  ('see-rce'),
  ('see-participant-guides'),
  ('manage-roles'),
  ('manage-discord-ids');

-- Make a new table to store the role-permission relationship
CREATE TABLE role_permissions_new (
  role_id bigint(20) UNSIGNED NOT NULL,
  permission_id bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (role_id, permission_id),
  FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE,
  FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE
);

INSERT INTO role_permissions_new(role_id, permission_id)
  SELECT role_id, permission_id
  FROM role_permissions
  JOIN permissions WHERE permissions.name = role_permissions.permission;

-- Drop the old table
DROP TABLE role_permissions;
RENAME TABLE role_permissions_new TO role_permissions;

-- Add missing constraints
ALTER TABLE `roles` ADD CONSTRAINT UNIQUE (name);
ALTER TABLE `member_roles` ADD CONSTRAINT UNIQUE (badge_no, role_id);
