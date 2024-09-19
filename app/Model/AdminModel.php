<?php

namespace App\Model;

use App\Core\Constants;
use App\Core\Database;
use Exception;
use PDO;

/**
 * Admin Model Functions
 */
class AdminModel
{
    /** @var Database */
    private $instance;

    public function __construct()
    {
        $this->instance = Database::getInstance();
    }

    /**
     * This function checks whether the user is authenticated or not.
     * @return mixed
     */
    public function signIn()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST" && ! empty($_POST['username']) &&
            ! empty($_POST['password'])) {
            $connection = $this->instance->getConnection();
            $name = trim($_POST['username']);
            $password = trim($_POST['password']);
            $tableAdmin = Constants::TABLE_NAME_ADMIN;
            $columnUsername = Constants::COLUMN_USERNAME;
            $columnPassword = Constants::COLUMN_PASSWORD;

            try {
                $connection->beginTransaction();
                $result = $connection->prepare(
                    "SELECT * FROM $tableAdmin WHERE $columnUsername = :name AND
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
                return true;
            }
        }

        return false;
    }

    /**
     * This function loads the admin dashboard.
     * @return null
     */
    public function loadAdminDashboard()
    {
        $connection = $this->instance->getConnection();
        $tableSubjects = Constants::TABLE_NAME_SUBJECTS;
        $tableTeachers = Constants::TABLE_NAME_TEACHERS;

        $subjects = $connection->prepare("SELECT * FROM $tableSubjects;");
        $subjects->execute();
        $_SESSION['subjects'] = $subjects->fetchAll();

        $teachers = $connection->prepare("SELECT * FROM $tableTeachers;");
        $teachers->execute();
        $_SESSION['teachers'] = $teachers->fetchAll();
    }

    /**
     * This function adds new subject based on the condition.
     * @return mixed
     */
    public function addSubject()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST" && ! empty($_POST['subject_code']) &&
            ! empty($_POST['subject_name'])) {
            $connection = $this->instance->getConnection();
            $subjectCode = strtoupper(trim($_POST['subject_code']));
            $subjectName = trim($_POST['subject_name']);
            $tableSubject = Constants::TABLE_NAME_SUBJECTS;
            $columnSubjectCode = Constants::COLUMN_SUBJECT_CODE;
            $columnSubjectName = Constants::COLUMN_SUBJECT_NAME;

            if (! empty($subjectName) && ! empty($subjectCode)) {
                try {
                    $connection->beginTransaction();
                    $result = $connection->prepare("SELECT * FROM $tableSubject WHERE $columnSubjectCode = :code");
                    $result->bindParam(':code', $subjectCode);
                    $result->execute();
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                    if (! empty($row) && $row[$columnSubjectCode] == $subjectCode) {
                        return Constants::EXCEPTION_UNIQUE;
                    }
                    $statement =  $connection->prepare(
                        "INSERT INTO $tableSubject ($columnSubjectCode, $columnSubjectName) VALUES (:code, :name)"
                    );
                    $statement->bindParam(':code', $subjectCode);
                    $statement->bindParam(':name', $subjectName);

                    if (strlen($subjectName) > 4) {
                        $connection->commit();
                        $statement->execute();
                    } else {
                        $connection->rollback();
                        return Constants::EXCEPTION_NAME_LENGTH;
                    }
                } catch (Exception $e) {
                    throw new Exception("Message: " . $e->getMessage());
                }
            }

            return false;
        }
    }

    /**
     * This function returns the subject name based on the subject code.
     * @param string $_code
     * @return string
     */
    public function getSubjectName($_code)
    {
        $tableSubject = Constants::TABLE_NAME_SUBJECTS;
        $columnSubjectCode = Constants::COLUMN_SUBJECT_CODE;
        $columnSubjectName = Constants::COLUMN_SUBJECT_NAME;
        $connection = $this->instance->getConnection();
        $subjects = $connection->prepare(
            "SELECT $columnSubjectName FROM $tableSubject where $columnSubjectCode = :code;"
        );
        $subjects->bindParam(':code', $_code);
        $subjects->execute();
        $row = $subjects->fetch(PDO::FETCH_ASSOC);
        return $row[$columnSubjectName];
    }

    /**
     * This function adds new teacher based on the condition.
     * @return mixed
     */
    public function addTeacher()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST" && ! empty($_POST['name']) &&
            ! empty($_POST['username']) && ! empty($_POST['password']) && ! empty($_POST['subject_code'])) {
            $connection = $this->instance->getConnection();
            $subjectCode = json_encode($_POST['subject_code']);
            $name = trim($_POST['name']);
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $hashPassword = md5($password);
            $tableTeachers = Constants::TABLE_NAME_TEACHERS;
            $columnSubjectCode = Constants::COLUMN_SUBJECT_CODE;
            $columnName = Constants::COLUMN_NAME;
            $columnUsername = Constants::COLUMN_USERNAME;
            $columnPassword = Constants::COLUMN_PASSWORD;

            if (! empty($name) && ! empty($subjectCode) && ! empty($username) && ! empty($password)) {
                try {
                    $connection->beginTransaction();
                    $result = $connection->prepare("SELECT * FROM $tableTeachers WHERE $columnUsername = :username");
                    $result->bindParam(':username', $username);
                    $result->execute();
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                    if (! empty($row) && $row[$columnUsername] == $username) {
                        return Constants::EXCEPTION_UNIQUE;
                    }
                    $statement =  $connection->prepare(
                        "INSERT INTO $tableTeachers ($columnName, $columnUsername, $columnPassword, $columnSubjectCode)
                        VALUES (:name, :username, :password, :code)"
                    );
                    $statement->bindParam(':name', $name);
                    $statement->bindParam(':username', $username);
                    $statement->bindParam(':password', $hashPassword);
                    $statement->bindParam(':code', $subjectCode);
                    if (strlen($username) > 4) {
                        $connection->commit();
                        $statement->execute();
                    } else {
                        $connection->rollback();
                        return Constants::EXCEPTION_NAME_LENGTH;
                    }
                } catch (Exception $e) {
                    throw new Exception("Message: " . $e->getMessage());
                }
            }

            return false;
        }
    }
}
