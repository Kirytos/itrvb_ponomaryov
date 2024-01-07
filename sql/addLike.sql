CREATE TABLE likes
(
    uuid         UUID PRIMARY KEY,
    author_uuid  UUID NOT NULL,
    article_uuid UUID NOT NULL,
    FOREIGN KEY (article_uuid) REFERENCES articles (uuid),
    FOREIGN KEY (author_uuid) REFERENCES users (uuid)
);