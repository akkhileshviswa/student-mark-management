<?php

namespace App\Model;

use App\Core\Constants;
use App\Core\Database;
use Exception;
use PDO;

/**
 * Teacher Model Functions
 */
class TeacherModel
{
    /** @var Database */
    private $instance;

    /**
     * TeacherModel Constructor
     */
    public function __construct(Database $database = null)
    {
        $this->instance = $database ? $database : Database::getInstance();
    }

    /**
     * This function checks whether the user is authenticated or not.
     * @return mixed
     */
    public function signIn()
    {
        if ($_SERVER['REQUEST_METHOD'] == Constants::HTTP_POST && ! empty($_POST['username']) &&
            ! empty($_POST['password'])) {
            $connection = $this->instance->getConnection();
            $name = trim($_POST['username']);
            $password = md5(trim($_POST['password']));
            $table = Constants::TABLE_NAME_TEACHERS;
            $columnUsername = Constants::COLUMN_USERNAME;
            $columnPassword = Constants::COLUMN_PASSWORD;

            try {
                $connection->beginTransaction();
                $result = $connection->prepare(
                    "SELECT * FROM $table WHERE $columnUsername = :name AND
                    $columnPassword = :password"
                );
                $result->bindParam(':name', $name);
                $result->bindParam(':password', $password);
                $result->execute();
                if ($name != "") {
                    $connection->commit();
                } else {
                    $connection->rollback();

                    return false;
                }
                if (! $result) {
                    throw new Exception("Error in Selecting the user.");
                }
            } catch (Exception $e) {
                throw new Exception("Message: " . $e->getMessage());
            }

            $row = $result->fetch();
            if (! empty($row) && $row[Constants::COLUMN_USERNAME] == $name &&
                $row[Constants::COLUMN_PASSWORD] == $password) {
                $_SESSION['teacher_id'] = $row[Constants::COLUMN_ID];
                $_SESSION['teacher_name'] = $row[Constants::COLUMN_NAME];
                $_SESSION['teacher_subject_codes'] = json_decode($row[Constants::COLUMN_SUBJECT_CODE]);

                return true;
            }
        }

        return false;
    }

    /**
     * This function checks whether the user is authenticated or not.
     * @return bool
     */
    public function loadDashboard()
    {
        $connection = $this->instance->getConnection();
        $tableStudents = Constants::TABLE_NAME_STUDENTS;
        $columnTeacherId = Constants::COLUMN_TEACHER_ID;
        $teacherId = $_SESSION['teacher_id'];

        // $students = $connection->prepare("SELECT * FROM $tableStudents");

        $students = $connection->prepare("SELECT * FROM $tableStudents WHERE $columnTeacherId = :id;");
        $students->bindParam(':id', $teacherId);
        $students->execute();
        $_SESSION['students'] = $students->fetchAll();
    }

    /**
     * This function is used to get the  student based on id.
     * @param int $_id
     *
     * @return mixed
     */
    public function getStudentById($_id)
    {
        try {
            $connection = $this->instance->getConnection();

            $result = $connection->prepare("SELECT * FROM students WHERE id = :id");
            $result->bindParam(':id', $_id);
            $result->execute();

            return $result->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Message: " . $e->getMessage());
        }
    }

    /**
     * This function is used to update the student mark.
     * @param string $_email
     * @param int $_mark
     * @param string $_subjectCode
     * @param string $_teacherId
     *
     * @return mixed
     */
    public function updateStudentMark($_email, $_mark, $_subjectCode, $_teacherId)
    {
        $table = Constants::TABLE_NAME_STUDENTS;
        $columnMark = Constants::COLUMN_MARK;
        $columnEmail = Constants::COLUMN_EMAIL;
        $columnSubjectcode = Constants::COLUMN_SUBJECT_CODE;
        $columnTeacherId = Constants::COLUMN_TEACHER_ID;

        try {
            $connection = $this->instance->getConnection();
            $connection->beginTransaction();

            $result = $connection->prepare("UPDATE $table SET $columnMark = :mark WHERE $columnEmail = :email AND
                $columnSubjectcode = :subjectCode AND $columnTeacherId = :teacherId");
            $result->bindParam(':mark', $_mark);
            $result->bindParam(':email', $_email);
            $result->bindParam(':subjectCode', $_subjectCode);
            $result->bindParam(':teacherId', $_teacherId);
            $result->execute();
            $connection->commit();
        } catch (Exception $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw new Exception("Message: " . $e->getMessage());
        }
    }

    /**
     * This function is used to update the student mark.
     * @param string $_email
     * @param int $_mark
     * @param string $_subjectCode
     * @param string $_teacherId
     *
     * @return mixed
     */
    public function updateStudent($_id, $_name, $_email, $_mark, $_subjectCode)
    {
        $table = Constants::TABLE_NAME_STUDENTS;
        $columnId = Constants::COLUMN_ID;
        $columnName = Constants::COLUMN_NAME;
        $columnMark = Constants::COLUMN_MARK;
        $columnEmail = Constants::COLUMN_EMAIL;
        $columnSubjectcode = Constants::COLUMN_SUBJECT_CODE;
        if (! $this->isStudentCreatedByTeacher($_id)) {
            return Constants::EXCEPTION_AUTHORIZATION_ERROR;
        }

        try {
            $connection = $this->instance->getConnection();
            $row = $this->getStudentByEmailAndTeacherIdAndSubject($_email, $_subjectCode, $_SESSION['teacher_id']);
            if (! empty($row) && isset($row[$columnId]) && $row[$columnId] != $_id) {
                return Constants::EXCEPTION_UNIQUE;
            }
            $connection->beginTransaction();
            $result = $connection->prepare("UPDATE $table SET $columnName = :name, $columnEmail = :email,
                $columnSubjectcode = :subjectCode, $columnMark = :mark WHERE $columnId = :id");
            $result->bindParam(':id', $_id);
            $result->bindParam(':name', $_name);
            $result->bindParam(':mark', $_mark);
            $result->bindParam(':email', $_email);
            $result->bindParam(':subjectCode', $_subjectCode);

            $result->execute();
            $connection->commit();

            return true;
        } catch (Exception $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw new Exception("Message: " . $e->getMessage());
        }
    }

    /**
     * This function is used to check whether the student is created by the teacher.
     * @param string $_id
     * @return bool
     */
    public function isStudentCreatedByTeacher($_studentId)
    {
        $student = $this->getStudentById($_studentId);

        return $student[Constants::COLUMN_TEACHER_ID] == $_SESSION['teacher_id'];
    }

    /**
     * This function is used to update the student mark.
     * @param string $_id
     * @return mixed
     */
    public function deleteStudent($_id)
    {
        $table = Constants::TABLE_NAME_STUDENTS;
        $columnId = Constants::COLUMN_ID;

        try {
            $connection = $this->instance->getConnection();
            $connection->beginTransaction();
            $result = $connection->prepare("DELETE FROM $table WHERE $columnId = :id");
            $result->bindParam(':id', $_id);
            $result->execute();
            $connection->commit();

            return true;
        } catch (Exception $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw new Exception("Message: " . $e->getMessage());
        }
    }

    /**
     * This function is used to get the student details based on email, teacher id, subject code and
     * return the result based on the fetch type.
     * @param string $_email
     * @param string $_teacherId
     * @param string $_subjectCode
     * @param string $_fetch_type
     *
     * @return mixed
     */
    public function getStudentByEmailAndTeacherIdAndSubject(
        $_email,
        $_subjectCode,
        $_teacherId,
        $_fetch_type = PDO::FETCH_ASSOC
    ) {
        $table = Constants::TABLE_NAME_STUDENTS;
        $columnEmail = Constants::COLUMN_EMAIL;
        $columnSubjectcode = Constants::COLUMN_SUBJECT_CODE;
        $columnTeacherId = Constants::COLUMN_TEACHER_ID;

        try {
            $connection = $this->instance->getConnection();
            $connection->beginTransaction();
            $result = $connection->prepare("SELECT * FROM $table WHERE $columnEmail = :email AND
                $columnSubjectcode = :subjectCode AND $columnTeacherId = :teacherId");
            $result->bindParam(':email', $_email);
            $result->bindParam(':subjectCode', $_subjectCode);
            $result->bindParam(':teacherId', $_teacherId);
            $result->execute();
            $connection->commit();

            return $result->fetch($_fetch_type);
        } catch (Exception $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw new Exception("Message: " . $e->getMessage());
        }
    }

    /**
     * This function adds a new student
     * @param string $_name
     * @param string $_email
     * @param string $_mark
     * @param string $_subjectCode
     *
     * @return mixed
     */
    public function addStudent($_name, $_email, $_mark, $_subjectCode)
    {
        $table = Constants::TABLE_NAME_STUDENTS;
        $columnName = Constants::COLUMN_NAME;
        $columnEmail = Constants::COLUMN_EMAIL;
        $columnMark = Constants::COLUMN_MARK;
        $columnSubjectcode = Constants::COLUMN_SUBJECT_CODE;
        $columnTeacherId = Constants::COLUMN_TEACHER_ID;
        $teacherId = $_SESSION['teacher_id'];

        try {
            $connection = $this->instance->getConnection();
            $row = $this->getStudentByEmailAndTeacherIdAndSubject($_email, $_subjectCode, $teacherId);

            if (isset($row[$columnEmail]) && $row[$columnEmail] == $_email) {
                $this->updateStudentMark($_email, $_mark, $_subjectCode, $teacherId);

                return [
                    true,
                    $this->getStudentByEmailAndTeacherIdAndSubject($_email, $_subjectCode, $teacherId, PDO::FETCH_OBJ)
                ];
            }
            $connection->beginTransaction();
            $statement =  $connection->prepare(
                "INSERT INTO $table ($columnName, $columnEmail, $columnSubjectcode, $columnMark, $columnTeacherId)
                VALUES (:name, :email, :subjectCode, :mark, :teacherId)"
            );

            $statement->bindParam(':name', $_name);
            $statement->bindParam(':email', $_email);
            $statement->bindParam(':subjectCode', $_subjectCode);
            $statement->bindParam(':mark', $_mark);
            $statement->bindParam(':teacherId', $teacherId);
            $statement->execute();
            $connection->commit();

            return [
                true,
                $this->getStudentByEmailAndTeacherIdAndSubject($_email, $_subjectCode, $teacherId, PDO::FETCH_OBJ)
            ];
        } catch (Exception $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }

            throw new Exception("Message: " . $e->getMessage());
        }
    }
}
