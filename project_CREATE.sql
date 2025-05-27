CREATE TABLE application (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    bio TEXT NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE languages (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    PRIMARY KEY (id)
);

INSERT INTO languages VALUES
(1, 'Pascal'),
(2, 'C'),
(3, 'C++'),
(4, 'JavaScript'),
(5, 'PHP'),
(6, 'Python'),
(7, 'Java'),
(8, 'Haskell'),
(9, 'Clojure'),
(10, 'Prolog'),
(11, 'Scala'),
(12, 'Go');

CREATE TABLE application_languages (
    application_id INT(10) UNSIGNED NOT NULL,
    language_id INT(10) UNSIGNED NOT NULL,
    FOREIGN KEY (application_id) REFERENCES application(id),
    FOREIGN KEY (language_id) REFERENCES languages(id)
);

CREATE TABLE userspr (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    application_id INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (application_id) REFERENCES application(id)
);
