CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    fio VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('male', 'female') NOT NULL,
    bio TEXT NOT NULL
);

CREATE TABLE langs ( lang_id INTEGER PRIMARY KEY, lang_name CHARACTER VARYING(30));

INSERT INTO langs VALUES (1, 'Pascal'), (2, 'C'), (3, 'C++'), (4, 'JavaScript'), (5, 'PHP'),
(6, 'Python'), (7, 'Java'), (8, 'Haskell'), (9, 'Clojure'), (10, 'Prolog'), (11, 'Scala'), (12, 'Go');

CREATE TABLE users_languages ( user_id INTEGER, lang_id INTEGER, PRIMARY KEY (user_id, lang_id), 
FOREIGN KEY (user_id) REFERENCES users (user_id), FOREIGN KEY (lang_id) REFERENCES langs (lang_id) );