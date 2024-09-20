# Student Mark Management

## Prerequisites
- **PHP:** ^8.0
- **MySQL:** 8.0

## Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/akkhileshviswa/student-mark-management.git
   ```
   This will clone the repo in your local, pointing to the main branch. The logic-level codes are still in the `feature/mark-management-system` branch. 

2. **Checkout to the Feature Branch**
   ```bash
   git fetch origin feature/mark-management-system && git checkout feature/mark-management-system
   ```

3. **Install Dependencies**
   Run the following command to install the necessary packages:
   ```bash
   composer install
   ```

4. **Database Setup**
   I have created a database called `student_mark_management` for this project. Run the following commands to create the necessary tables:
   ```sql
   USE student_mark_management;

   CREATE TABLE `admin` (
       `username` VARCHAR(20) DEFAULT NULL,
       `password` VARCHAR(100) DEFAULT NULL,
       UNIQUE KEY `username` (`username`)
   );

   INSERT INTO `admin` (`username`, `password`) VALUES ('your_username', 'your_password'); -- Insert your credentials

   CREATE TABLE `subjects` (
       `subject_code` VARCHAR(20) DEFAULT NULL,
       `subject_name` VARCHAR(100) DEFAULT NULL,
       UNIQUE KEY `subject_code` (`subject_code`)
   );

   CREATE TABLE `teachers` (
       `id` INT NOT NULL AUTO_INCREMENT,
       `name` VARCHAR(64) DEFAULT NULL,
       `username` VARCHAR(100) DEFAULT NULL,
       `password` VARCHAR(150) DEFAULT NULL,
       `subject_code` JSON DEFAULT NULL,
       PRIMARY KEY (`id`),
       UNIQUE KEY `username` (`username`)
   );

   CREATE TABLE `students` (
       `id` INT NOT NULL AUTO_INCREMENT,
       `name` VARCHAR(64) DEFAULT NULL,
       `email` VARCHAR(100) DEFAULT NULL,
       `subject_code` VARCHAR(20) DEFAULT NULL,
       `mark` DECIMAL(5,2) DEFAULT NULL,
       `teacher_id` INT DEFAULT NULL,
       PRIMARY KEY (`id`),
       KEY `teacher_id` (`teacher_id`),
       KEY `studentmailsubject` (`email`, `subject_code`, `teacher_id`),
       CONSTRAINT `students_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`)
   );
   ```

5. **Environment Configuration**
   Create a file called `.env` with the contents present in the `.env_sample` file. Configure your MySQL username, password, and database.

6. **Accessing the Portal**
   I have created a virtual host and added it to the host file. You can also access the portal without a virtual host:
   - **Admin:** [http://localhost/student-mark-management/index.php/admin](http://localhost/student-mark-management/index.php/admin)
   - **Teacher:** [http://localhost/student-mark-management/index.php/home](http://localhost/student-mark-management/index.php/home)
