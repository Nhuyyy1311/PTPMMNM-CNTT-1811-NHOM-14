
CREATE DATABASE IF NOT EXISTS xep_thoi_khoa_bieu
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE xep_thoi_khoa_bieu;

CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    student_count INT NOT NULL DEFAULT 30,
    department VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    capacity INT NOT NULL DEFAULT 50,
    room_type ENUM('Lý thuyết','Phòng máy','Thực hành')
        NOT NULL DEFAULT 'Lý thuyết'
);

CREATE TABLE IF NOT EXISTS slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_of_week TINYINT NOT NULL,
    slot_number TINYINT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    UNIQUE KEY unique_slot (day_of_week, slot_number)
);

CREATE TABLE IF NOT EXISTS lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE,
    subject_name VARCHAR(150) NOT NULL,
    teacher_id INT NOT NULL,
    class_id INT NOT NULL,
    required_room_type VARCHAR(30) NOT NULL DEFAULT 'Lý thuyết',
    duration_slots TINYINT NOT NULL DEFAULT 1,
    is_locked TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('Chưa xếp','Đã xếp') NOT NULL DEFAULT 'Chưa xếp',
    FOREIGN KEY (teacher_id) REFERENCES teachers(id),
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id INT NOT NULL,
    room_id INT NOT NULL,
    slot_id INT NOT NULL,
    version_id INT DEFAULT NULL,
    is_locked TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_lesson (lesson_id),
    UNIQUE KEY unique_room_slot (room_id, slot_id),
    UNIQUE KEY unique_version_lesson (version_id, lesson_id),
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (slot_id) REFERENCES slots(id)
);

CREATE TABLE IF NOT EXISTS schedule_versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    version_name VARCHAR(100) NOT NULL,
    total_scheduled INT NOT NULL DEFAULT 0,
    total_violations INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS unavailability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NULL,
    class_id INT NULL,
    room_id INT NULL,
    slot_id INT NOT NULL,
    reason VARCHAR(255),
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES slots(id) ON DELETE CASCADE
);

INSERT IGNORE INTO slots
(day_of_week, slot_number, start_time, end_time)
VALUES
(2,1,'07:00:00','09:00:00'),
(2,2,'09:00:00','11:00:00'),
(2,3,'13:00:00','15:00:00'),
(2,4,'15:00:00','17:00:00'),
(3,1,'07:00:00','09:00:00'),
(3,2,'09:00:00','11:00:00'),
(3,3,'13:00:00','15:00:00'),
(3,4,'15:00:00','17:00:00'),
(4,1,'07:00:00','09:00:00'),
(4,2,'09:00:00','11:00:00'),
(4,3,'13:00:00','15:00:00'),
(4,4,'15:00:00','17:00:00'),
(5,1,'07:00:00','09:00:00'),
(5,2,'09:00:00','11:00:00'),
(5,3,'13:00:00','15:00:00'),
(5,4,'15:00:00','17:00:00'),
(6,1,'07:00:00','09:00:00'),
(6,2,'09:00:00','11:00:00'),
(6,3,'13:00:00','15:00:00'),
(6,4,'15:00:00','17:00:00');

INSERT IGNORE INTO teachers (code, full_name, email, department)
VALUES
('GV001','Nguyễn Văn An','an@example.com','Công nghệ thông tin'),
('GV002','Trần Thị Bình','binh@example.com','Công nghệ thông tin'),
('GV003','Lê Văn Cường','cuong@example.com','Khoa học cơ bản'),
('GV004','Phạm Thị Dung','dung@example.com','Công nghệ thông tin'),
('GV005','Hoàng Văn Minh','minh@example.com','Khoa học cơ bản'),
('GV006','Đỗ Thị Lan','lan@example.com','Công nghệ thông tin');

INSERT IGNORE INTO classes (code, name, student_count, department)
VALUES
('CNTT01','Lớp CNTT 01',40,'Công nghệ thông tin'),
('CNTT02','Lớp CNTT 02',35,'Công nghệ thông tin'),
('CNTT03','Lớp CNTT 03',45,'Công nghệ thông tin'),
('CNTT04','Lớp CNTT 04',30,'Công nghệ thông tin'),
('CNTT05','Lớp CNTT 05',50,'Công nghệ thông tin');

INSERT IGNORE INTO rooms (code, capacity, room_type)
VALUES
('A101',50,'Lý thuyết'),
('A102',40,'Lý thuyết'),
('A103',60,'Lý thuyết'),
('B201',40,'Phòng máy'),
('B202',35,'Phòng máy');
