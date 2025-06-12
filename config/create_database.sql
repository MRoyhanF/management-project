-- Tabel Users
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100),
    role ENUM('admin', 'manager', 'anggota') NOT NULL
);

-- Tabel Projects
CREATE TABLE projects (
    id_project INT AUTO_INCREMENT PRIMARY KEY,
    nama_project VARCHAR(100),
    deskripsi TEXT,
    tanggal_mulai DATE,
    tanggal_deadline DATE,
    id_manager INT,
    FOREIGN KEY (id_manager) REFERENCES users(id_user) ON DELETE SET NULL
);

-- Tabel Tasks
CREATE TABLE tasks (
    id_task INT AUTO_INCREMENT PRIMARY KEY,
    id_project INT,
    judul_task VARCHAR(100),
    deskripsi_task TEXT,
    deadline_task DATE,
    status ENUM('belum mulai', 'proses', 'selesai') DEFAULT 'belum mulai',
    progress INT DEFAULT 0,
    FOREIGN KEY (id_project) REFERENCES projects(id_project) ON DELETE CASCADE
);

-- Tabel Task Assignments
CREATE TABLE task_assignments (
    id_assignment INT AUTO_INCREMENT PRIMARY KEY,
    id_task INT,
    id_user INT,
    tanggal_ditugaskan DATE,
    FOREIGN KEY (id_task) REFERENCES tasks(id_task) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
);