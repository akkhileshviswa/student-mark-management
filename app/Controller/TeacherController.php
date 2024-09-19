<?php

namespace App\Controller;

use App\Core\Constants;
use App\Core\Routes;
use App\Model\AdminModel;
use App\Model\TeacherModel;

/**
 * Teacher Controller Functions
 */
class TeacherController
{
    /** @var AdminModel */
    private $adminModel;

    /** @var TeacherModel */
    private $teacherModel;

    /**
     * TeacherController Constructor
     */
    public function __construct(AdminModel $_adminModel = null, TeacherModel $_teacherModel = null)
    {
        $this->adminModel = $_adminModel ?? new AdminModel();
        $this->teacherModel = $_teacherModel ?? new TeacherModel();
    }

    /**
     * This function will load the view files based on the input
     * @param string $_viewName
     */
    protected function loadView($_viewName)
    {
        Routes::load($_viewName);
    }

    /**
     * This method loads login page on get request.
     * @return null
     */
    public function loadLogin()
    {
        $_SESSION['teacherloggedin'] = 0;
        $this->loadView("TeacherLogin");
    }

    /**
     * This method calls signIn method in TeacherModel class based on the result it loads the desired
     * view page.
     * @return null
     */
    public function signIn()
    {
        $result = $this->teacherModel->signIn();
        if ($result) {
            $_SESSION['teacherloggedin'] = 1;
            $this->teacherModel->loadDashboard();
            $this->loadView("TeacherDashboard");
        } else {
            $_SESSION['error_message'] = "Username or Password is incorrect!!";
            $this->loadLogin();
        }
    }

    /**
     * This method loads student dashboard on get request.
     * @return null
     */
    public function loadDashboard()
    {
        if ($_SESSION['teacherloggedin']) {
            $this->teacherModel->loadDashboard();
            $this->loadView("TeacherDashboard");
        } else {
            $_SESSION['error_message'] = "Login to continue";
            $this->loadLogin();
        }
    }

    /**
     * This method loads student edit form on get request.
     * @return null
     */
    public function loadEditStudent()
    {
        if ($_SESSION['teacherloggedin'] && ! empty($_GET['student_id'])) {
            $_SESSION['update_student'] = $this->teacherModel->getStudentById($_GET['student_id']);
            $this->loadView("TeacherEditStudent");
        } else {
            $_SESSION['error_message'] = "Login to continue";
            $this->loadLogin();
        }
    }

    /**
     * This method adds new student.
     * @return null
     */
    public function addStudent()
    {
        if ($_SESSION['teacherloggedin']) {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $mark = trim($_POST['marks']);
            $subjectCode = trim($_POST['subject_code']);
            $message = '';
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['success'] = false;
                $message = $response['error'] = 'Enter valid email!';
            }
            if ($_SERVER['REQUEST_METHOD'] == Constants::HTTP_POST && ! empty($name) && ! empty($email) &&
                ! empty($mark) && ! empty($subjectCode) && empty($message)) {
                [$result, $student] = $this->teacherModel->addStudent($name, $email, $mark, $subjectCode);
                $student->subject_name = $this->adminModel->getSubjectName($subjectCode);
                if ($result) {
                    $response['success'] = true;
                    $response['student'] = $student;
                } else {
                    $response['success'] = false;
                    $response['error'] = 'Student creation has issue!';
                }
            }

            echo json_encode($response);
        }
    }

    /**
     * This method updates student based on id.
     * @return null
     */
    public function updateStudent()
    {
        $id = trim($_POST['student_id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $mark = trim($_POST['marks']);
        $subjectCode = trim($_POST['subject_code']);
        if ($_SESSION['teacherloggedin'] && $_SERVER['REQUEST_METHOD'] == Constants::HTTP_POST &&
            ! empty($id) && ! empty($name) && ! empty($email) && ! empty($mark) &&
            ! empty($subjectCode)) {
            $result = $this->teacherModel->updateStudent($id, $name, $email, $mark, $subjectCode);
            if ($result == Constants::EXCEPTION_UNIQUE) {
                $_SESSION['error_message'] = "Already a student exists with same details. Update values!";
            } else {
                $_SESSION['success_message'] = "Student updated successfully!";
            }
            $this->teacherModel->loadDashboard();
            $this->loadView("TeacherDashboard");
        } else {
            $_SESSION['error_message'] = "Login to continue";
            $this->loadLogin();
        }
    }

    /**
     * This method deletes student based on id.
     * @return null
     */
    public function deleteStudent()
    {
        $id = trim($_GET['id']);
        if ($_SESSION['teacherloggedin'] && $_SERVER['REQUEST_METHOD'] == Constants::HTTP_GET &&
            ! empty($id)) {
            $this->teacherModel->deleteStudent($id);
            $this->teacherModel->loadDashboard();
            $this->loadView("TeacherDashboard");
        } else {
            $_SESSION['error_message'] = "Login to continue";
            $this->loadLogin();
        }
    }
}
