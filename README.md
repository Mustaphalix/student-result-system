# Student Result System

A SIWES project: Web-based system for managing student results using PHP and MySQL. Admins add students and results; students view by matric number.

## Features
- Add students: Name, Matric No, Department.
- Add results: For courses like CYB321 (Intro to Cyber), CSC321 (Web Design)—auto-grades (A-F).
- View results: Secure table display.
- Secure: Prepared statements; brown/chocolate UI with Bootstrap.

## Tech Stack
- Backend: PHP, MySQL.
- Frontend: HTML, CSS, Bootstrap.
- Environment: XAMPP.

## Setup & Run
1. Clone this repo: `git clone https://github.com/Mustaphalix/student-result-system.git`
2. Install XAMPP; start Apache/MySQL.
3. In phpMyAdmin: Create DB `student_result_db` and run schema (below).
4. Place folder in `htdocs` (e.g., `/opt/lampp/htdocs/student_result`).
5. Visit: `http://localhost/student_result/add_student.php`.

## Database Schema
```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    reg_no VARCHAR(50) UNIQUE,
    department VARCHAR(100)
);
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    subject VARCHAR(100),
    score INT,
    grade VARCHAR(2),
    FOREIGN KEY (student_id) REFERENCES students(id)
);
