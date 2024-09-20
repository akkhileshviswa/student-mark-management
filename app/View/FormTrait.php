<?php

namespace App\View;

use App\Model\AdminModel;

trait FormTrait
{
    // This function is used to use the header code for admin and teacher login forms.
    public function renderHeader()
    {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login</title>
            <link rel="stylesheet" type="text/css" href="http://localhost/student-mark-management/assets/css/login.css">
            <script src="http://localhost/student-mark-management/assets/js/validation.js"></script>
        </head>
        <body>
        ';
    }

    // This function is used to reuse the code for admin and teacher login forms.
    public function renderLoginForm()
    {
        echo '
        <div class="input-group">
            <label for="username">Username</label>
            <div class="input-wrapper">
                <span class="icon">👤</span>
                <input type="text" id="username" name="username" placeholder="Username" 
                    value="' . (isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '') . '">
            </div>
            <br><span id="usernameerr"></span><br>
        </div>
        <div class="input-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
                <span class="icon">🔒</span>
                <input type="password" id="password" name="password" placeholder="Password" 
                    value="' . (isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '') . '">
            </div>
            <br><span id="passworderr"></span><br>
        </div>
        <input id="button" type="submit" value="Login">
        </form>
        </div>
        </body>
        </html>
        ';
    }

    // This function is used to use the header code for admin dashboard and subject teacher forms.
    public function renderDashboardHeader()
    {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Student Management System</title>
            <link rel="stylesheet" type="text/css" href="http://localhost/student-mark-management/assets/css/dashboard.css">
            <script src="http://localhost/student-mark-management/assets/js/validation.js"></script>
        </head>
        <body>
            <div class="container">
        ';
    }

    public function getSubjectName($_code)
    {
        $model = new AdminModel();

        return $model->getSubjectName($_code);
    }

    public function getMessages()
    {
        echo '
        <?php if (isset($_SESSION["error_message"])): ?>
            <span id="error_message">
            ' . htmlspecialchars($_SESSION["error_message"]) . '
            </span>
        <?php elseif (isset($_SESSION["success_message"])): ?>
            <span id="success_message">
            ' . htmlspecialchars($_SESSION["success_message"]) . '
            </span>
        <?php endif; ?>
        ';
    }
}
