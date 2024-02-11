DROP SEQUENCE IF EXISTS users_id_seq;

DROP TABLE IF EXISTS funkos;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS user_roles;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS users;

CREATE TABLE categories
(
    id         UUID PRIMARY KEY,
    name       VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    is_deleted BOOLEAN NOT NULL DEFAULT false
);

CREATE TABLE funkos
(
    id          UUID PRIMARY KEY,
    name        VARCHAR(255),
    image       VARCHAR(255),
    price       DECIMAL(10, 2),
    stock       INT,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP,
    category_id UUID,
    FOREIGN KEY (category_id) REFERENCES categories (id)
);

CREATE TABLE user_roles
(
    user_id INT,
    roles   VARCHAR(255)
);

create sequence users_id_seq start 4;
CREATE TABLE users
(
    id         INT PRIMARY KEY,
    username   VARCHAR(255) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    name       VARCHAR(255) NOT NULL,
    surnames   VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL,
    updated_at TIMESTAMP,
    is_deleted BOOLEAN      NOT NULL,
    constraint user_email_key UNIQUE (email),
    constraint user_username_key UNIQUE (username)
);

insert into user_roles (user_id, roles)
values (1, 'USER'),
       (1, 'ADMIN'),
       (2, 'USER'),
       (2, 'USER'),
       (3, 'USER');

insert into categories (id, name, created_at, updated_at)
values ('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11', 'Marvel', now(), now()),
       ('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a12', 'Star Wars', now(), now()),
       ('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a13', 'DC', now(), now()),
       ('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a14', 'Pokemon', now(), now()),
       ('a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a15', 'Disney', now(), now());

INSERT INTO funkos (id, name, image, price, stock, created_at, updated_at, category_id)
VALUES ('247580e6-e3cd-48be-b50d-9b789f1483c1', 'Funko Pop! Harry Potter', 'http://localhost:8080/uploads/harry_potter.jpg', 12.99, 100, CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
       ('247580e6-e3cd-48be-b50d-9b789f1483c2', 'Funko Pop! Darth Vader', 'http://localhost:8080/uploads/darth_vader.jpg', 14.99, 50,
        CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
       ('247580e6-e3cd-48be-b50d-9b789f1483c3', 'Funko Pop! Batman', 'http://localhost:8080/uploads/batman.jpg', 9.99, 75, CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
       ('247580e6-e3cd-48be-b50d-9b789f1483c4', 'Funko Pop! Mickey Mouse', 'http://localhost:8080/uploads/mickey_mouse.jpg', 11.99, 80, CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11'),
       ('247580e6-e3cd-48be-b50d-9b789f1483c5', 'Funko Pop! SuperMan', 'http://localhost:8080/uploads/superman.jpg', 8.99, 120, CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP, 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11');

INSERT INTO users (id, username, password, name, surnames, email, created_at, updated_at, is_deleted)
VALUES (1, 'admin', '$2a$10$vPaqZvZkz6jhb7U7k/V/v.5vprfNdOnh4sxi/qpPRkYTzPmFlI9p2', 'admin', 'Adminson',
        'admin@example.com', '2024-01-30 10:00:00', NULL, false),
       (2, 'user', '$2a$12$RUq2ScW1Kiizu5K4gKoK4OTz80.DWaruhdyfi2lZCB.KeuXTBh0S.', 'John', 'Doe',
        'john.doe@example.com', '2024-01-30 10:00:00', NULL, false),
       (3, 'test', '$2a$10$Pd1yyq2NowcsDf4Cpf/ZXObYFkcycswqHAqBndE1wWJvYwRxlb.Pu', 'Jane', 'Smith',
        'jane.smith@example.com', '2024-01-30 10:00:00', NULL, false);