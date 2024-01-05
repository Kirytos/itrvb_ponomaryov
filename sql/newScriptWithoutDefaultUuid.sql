CREATE TABLE users
(
    uuid       UUID PRIMARY KEY,
    username   TEXT NOT NULL,
    first_name TEXT NOT NULL,
    last_name  TEXT NOT NULL
);

CREATE TABLE articles
(
    uuid        UUID PRIMARY KEY,
    author_uuid UUID,
    title       TEXT NOT NULL,
    text        TEXT NOT NULL,
    FOREIGN KEY (author_uuid) REFERENCES users (uuid)

);

CREATE TABLE comments
(
    uuid         UUID PRIMARY KEY,
    article_uuid UUID,
    author_uuid  UUID,
    text         TEXT NOT NULL,
    FOREIGN KEY (article_uuid) REFERENCES articles (uuid),
    FOREIGN KEY (author_uuid) REFERENCES users (uuid)
);