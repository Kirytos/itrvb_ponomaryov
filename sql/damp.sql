-- Вставка данных в таблицу "users"
INSERT INTO users (uuid, username, first_name, last_name) VALUES
                                                              ('1', 'user1', 'John', 'Doe'),
                                                              ('2', 'user2', 'Alice', 'Smith'),
                                                              ('3', 'user3', 'Bob', 'Johnson');

-- Вставка данных в таблицу "articles"
INSERT INTO articles (uuid, author_uuid, title, text) VALUES
                                                          ('1', '1', 'Introduction to SQLite', 'This is an introduction to SQLite.'),
                                                          ('2', '1', 'SQLite Queries', 'Learn how to write queries in SQLite.'),
                                                          ('3', '2', 'SQLite Data Manipulation', 'Working with data in SQLite.');

-- Вставка данных в таблицу "comments"
INSERT INTO comments (uuid, article_uuid, author_uuid, text) VALUES
                                                                 ('1', '1', '3', 'Great article!'),
                                                                 ('2', '1', '3', 'Nice explanation.'),
                                                                 ('3', '2', '1', 'Thanks for sharing!');