<?php

namespace App\Controller;

use App\Model\AdminModel;
use App\Core\Constants;
use App\Core\Routes;

/**
 * Admin Controller Functions
 */
class AdminController
{
    /** @var AdminModel */
    private $adminModel;

    /**
     * AdminController Constructor
     */
    public function __construct(AdminModel $_adminModel = null)
    {
        $this->adminModel = $_adminModel ?? new AdminModel();
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
        $_SESSION['adminloggedin'] = 0;
        $this->loadView('AdminLogin');
    }

    /**
     * This method calls signIn method in AdminModel class based on the
     * result it loads the desired view page.
     * @return null
     */
    public function signIn()
    {
        $result = $this->adminModel->signIn();
        if ($result) {
            $_SESSION['adminloggedin'] = 1;
            $this->adminModel->loadAdminDashboard();
            $this->loadView("AdminDashboard");
        } else {
            $_SESSION['error_message'] = "Username or Password is incorrect!!";
            $this->loadView("AdminLogin");
        }
    }

    /**
     * This method loads admin dashboard on get request.
     * @return null
     */
    public function loadDashboard()
    {
        if ($_SESSION['adminloggedin']) {
            $this->adminModel->loadAdminDashboard();
            $this->loadView('AdminDashboard');
        } else {
            $_SESSION['error_message'] = "Login to continue!!";
            $this->loadLogin();
        }
    }

    /**
     * This method loads add subject form on get request.
     * @return null
     */
    public function loadSubjectForm()
    {
        if ($_SESSION['adminloggedin']) {
            $_SESSION['addSubject'] = 1;
            $_SESSION['addTeacher'] = 0;
            $this->loadView('AdminAddSubjectTeacherForm');
        } else {
            $_SESSION['error_message'] = "Login to continue!!";
            $this->loadLogin();
        }
    }

    /**
     * This method loads add teacher form on get request.
     * @return null
     */
    public function loadTeacherForm()
    {
        if ($_SESSION['adminloggedin']) {
            $_SESSION['addTeacher'] = 1;
            $_SESSION['addSubject'] = 0;
            $this->loadView('AdminAddSubjectTeacherForm');
        } else {
            $_SESSION['error_message'] = "Login to continue!!";
            $this->loadLogin();
        }
    }

    /**
     * This method adds new teacher.
     * @return null
     */
    public function addTeacher()
    {
        $success_message = $error_message = '';
        $result = $this->adminModel->addTeacher();
        switch ($result) {
            case Constants::EXCEPTION_UNIQUE:
                $error_message = 'Username should be Unique!';

                break;
            case Constants::EXCEPTION_NAME_LENGTH:
                $error_message = 'Username should be greater than 4 characters!';

                break;

            default:
                $success_message = 'Teacher has been created successfully';
        }

        if ($error_message) {
            $_SESSION['error_message'] = $error_message;
        } else {
            $_SESSION['success_message'] = $success_message;
        }

        $this->loadDashboard();
    }

    /**
     * This method adds new subject.
     * @return null
     */
    public function addSubject()
    {
        $success_message = $error_message = '';
        $result = $this->adminModel->addSubject();
        switch ($result) {
            case Constants::EXCEPTION_UNIQUE:
                $error_message = 'Subject Code should be Unique!';

                break;
            case Constants::EXCEPTION_NAME_LENGTH:
                $error_message = 'Subject Name should be greater than 4 characters!';

                break;

            default:
                $success_message = 'Subject has been created successfully';
        }

        if ($error_message) {
            $_SESSION['error_message'] = $error_message;
        } else {
            $_SESSION['success_message'] = $success_message;
        }

        $this->loadDashboard();
    }
}
