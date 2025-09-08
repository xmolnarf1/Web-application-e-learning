CREATE TABLE users
(
    id       INT AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(255) UNIQUE NOT NULL,
    password CHAR(255)            NOT NULL
);
