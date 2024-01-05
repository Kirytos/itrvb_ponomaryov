CREATE TABLE users
(
    uuid       TEXT DEFAULT lower(hex(randomblob(4))) || '-' || lower(hex(randomblob(2))) || '-4' ||
                            substr(lower(hex(randomblob(2))), 2) || '-a' || substr(lower(hex(randomblob(2))), 2) ||
                            '-6' || substr(lower(hex(randomblob(2))), 2) || lower(hex(randomblob(6))) PRIMARY KEY,
    username   TEXT NOT NULL,
    first_name TEXT NOT NULL,
    last_name  TEXT NOT NULL
);

CREATE TABLE articles
(
    uuid        TEXT DEFAULT lower(hex(randomblob(4))) || '-' || lower(hex(randomblob(2))) || '-4' ||
                             substr(lower(hex(randomblob(2))), 2) || '-a' || substr(lower(hex(randomblob(2))), 2) ||
                             '-6' || substr(lower(hex(randomblob(2))), 2) || lower(hex(randomblob(6))) PRIMARY KEY,
    author_uuid UUID,
    title       TEXT NOT NULL,
    text        TEXT NOT NULL,
    FOREIGN KEY (author_uuid) REFERENCES users (uuid)

);

CREATE TABLE comments
(
    uuid         TEXT DEFAULT lower(hex(randomblob(4))) || '-' || lower(hex(randomblob(2))) || '-4' ||
                              substr(lower(hex(randomblob(2))), 2) || '-a' || substr(lower(hex(randomblob(2))), 2) ||
                              '-6' || substr(lower(hex(randomblob(2))), 2) || lower(hex(randomblob(6))) PRIMARY KEY,
    article_uuid UUID,
    author_uuid  UUID,
    text         TEXT NOT NULL,
    FOREIGN KEY (article_uuid) REFERENCES articles (uuid),
    FOREIGN KEY (author_uuid) REFERENCES users (uuid)
);