-- ============================================================
-- Student Result Management System
-- Database: student_result_db
-- ============================================================

CREATE DATABASE IF NOT EXISTS student_result_db;
USE student_result_db;

-- ------------------------------------------------------------
-- Table: students
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll_no VARCHAR(20) NOT NULL UNIQUE,
    enrollment_no VARCHAR(30) NOT NULL,
    name VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    semester VARCHAR(20) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Table: marks
-- One row per student (student_id is UNIQUE -> 1-to-1 relation)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL UNIQUE,
    subject1 INT NOT NULL,
    subject2 INT NOT NULL,
    subject3 INT NOT NULL,
    subject4 INT NOT NULL,
    subject5 INT NOT NULL,
    total INT NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    grade VARCHAR(5) NOT NULL,
    result_status VARCHAR(10) NOT NULL,
    CONSTRAINT fk_marks_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Sample data (so the project can be tested immediately)
-- ------------------------------------------------------------

INSERT INTO students (id, roll_no, enrollment_no, name, course, semester, email) VALUES
(1, '101', 'EN2023001', 'Aman Verma',  'B.Sc IT (Hons)', 'VII', 'aman.verma@example.com'),
(2, '102', 'EN2023002', 'Priya Sharma','B.Sc IT (Hons)', 'VII', 'priya.sharma@example.com'),
(3, '103', 'EN2023003', 'Rohit Singh', 'B.Sc IT (Hons)', 'VII', 'rohit.singh@example.com'),
(4, '104', 'EN2023004', 'Neha Gupta',  'B.Sc IT (Hons)', 'VII', 'neha.gupta@example.com'),
(5, '105', 'EN2023005', 'Karan Mehta', 'B.Sc IT (Hons)', 'VII', 'karan.mehta@example.com');

-- subject1..subject5 out of 100 each, total out of 500
INSERT INTO marks (student_id, subject1, subject2, subject3, subject4, subject5, total, percentage, grade, result_status) VALUES
(1, 85, 90, 78, 88, 92, 433, 86.60, 'A',  'PASS'),  -- good marks in all subjects -> PASS
(2, 45, 38, 60, 55, 70, 268, 53.60, 'C',  'FAIL'),  -- one subject (38) below 40 -> FAIL
(3, 95, 96, 91, 89, 93, 464, 92.80, 'A+', 'PASS'),  -- excellent marks -> PASS
(4, 40, 42, 41, 45, 44, 212, 42.40, 'D',  'PASS'),  -- borderline (exactly 40) -> PASS
(5, 60, 65, 55, 58, 62, 300, 60.00, 'B',  'PASS');
