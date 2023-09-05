CREATE TABLE uploaded_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    new_filename VARCHAR(255) NOT NULL,
    random_filename VARCHAR(5) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    delete_filename VARCHAR(5) NOT NULL,
    file_extension VARCHAR(10) NOT NULL,
    file_type VARCHAR(255) NOT NULL,
    file_size VARCHAR(20) NOT NULL,
    uploaded_date DATETIME NOT NULL,
    expiration_date DATETIME NOT NULL
);
