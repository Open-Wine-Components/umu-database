CREATE TABLE game (
    id VARCHAR(255) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    acronym VARCHAR(255)
);

CREATE TABLE gamerelease (
    umu_id VARCHAR(255) REFERENCES game(id),
    codename VARCHAR(255),
    store VARCHAR(255),
    notes TEXT
);
