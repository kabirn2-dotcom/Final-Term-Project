CREATE DATABASE IF NOT EXISTS fifatracker;
USE fifatracker;

CREATE TABLE: matches (
    MatchID INT AUTO_INCREMENT PRIMARY KEY,
    TeamA VARCHAR(255) NOT NULL,
    TeamB VARCHAR(255) NOT NULL,
    Date DATETIME NOT NULL
);

CREATE USER 'fifa_user'@'localhost'
INDENTIFIED BY 'StrongPassword123';

GRANT SELECT, INSERT, UPDATE, DELETE
ON fifatracker.*
TO 'fifa_user'@'localhost';

FLUSH PRIVILEGES;