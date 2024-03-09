CREATE TABLE members (
    badge_no VARCHAR(5) PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX members_email ON members(email);

CREATE TABLE sessions (
    session_id VARCHAR(100) PRIMARY KEY,
    badge_no VARCHAR(5) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (badge_no) REFERENCES members(badge_no)
);

CREATE TABLE login_links (
    login_code VARCHAR(100) PRIMARY KEY,
    badge_no VARCHAR(5) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (badge_no) REFERENCES members(badge_no)
);
