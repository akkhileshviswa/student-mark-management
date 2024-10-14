# Student Mark Management

## Prerequisites
- **PHP:** ^8.0
- **MySQL:** 8.0
- **Composer:** 2.7

## Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/akkhileshviswa/student-mark-management.git
   ```

2. **Install Dependencies**
   Run the following command to install the necessary packages:
   ```bash
   composer install
   ```

3. **Database Setup**
   I have created a database called `student_mark_management` for this project. Run the following commands to create the necessary tables:
   ```sql
   CREATE DATABASE student_mark_management;

   USE student_mark_management;

   CREATE TABLE `admin` (
       `username` VARCHAR(20) NOT NULL UNIQUE,
       `password` VARCHAR(100) NOT NULL
   );

   INSERT INTO `admin` (`username`, `password`) VALUES ('your_username', 'your_password'); -- Insert your credentials

   CREATE TABLE `subjects` (
       `subject_code` VARCHAR(20) NOT NULL UNIQUE,
       `subject_name` VARCHAR(100) NOT NULL
   );

   CREATE TABLE `teachers` (
       `id` INT AUTO_INCREMENT PRIMARY KEY,
       `name` VARCHAR(64) NOT NULL,
       `username` VARCHAR(100) NOT NULL UNIQUE,
       `password` VARCHAR(150) NOT NULL,
       `subject_code` JSON NOT NULL,
   );

   CREATE TABLE `students` (
       `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
       `name` VARCHAR(64) NOT NULL,
       `email` VARCHAR(100) NOT NULL,
       `subject_code` VARCHAR(20) NOT NULL,
       `mark` DECIMAL(5,2) DEFAULT NULL,
       `teacher_id` INT NOT NULL,
       KEY `teacher_id` (`teacher_id`),
       KEY `studentmailsubject` (`email`, `subject_code`, `teacher_id`),
       CONSTRAINT `students_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`)
   );
   ```

4. **Environment Configuration**
   Create a file called `.env` with the contents present in the `.env_sample` file. Configure your MySQL username, password, and database.

5. **Accessing the Portal**
   I have created a virtual host and added it to the host file. You can also access the portal without a virtual host:
   - **Admin:** [http://localhost/student-mark-management/index.php/admin](http://localhost/student-mark-management/index.php/admin)
   - **Teacher:** [http://localhost/student-mark-management/index.php/home](http://localhost/student-mark-management/index.php/home)
