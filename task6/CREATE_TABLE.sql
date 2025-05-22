CREATE TABLE admins (
     admin_id INT AUTO_INCREMENT PRIMARY KEY,
     fio VARCHAR(150) NOT NULL,
     login VARCHAR(50) NOT NULL UNIQUE,
     password VARCHAR(255) NOT NULL
);

INSERT INTO admins (fio, login, password) 
     VALUES ('Свиридченко Екатерина', 'ekaterinasv', '$2y$10$nn34K.jwRfDMU4mH2ejY3OAQtzAJRNNT8J1g8iLD2zVbzTnGjiObK'
);
