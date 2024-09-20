<?php

namespace App\Core;

use App\Controller\TeacherController;
use App\Controller\AdminController;

class Layout
{
    /**
     * Register the routes
     */
    public function __construct()
    {
        Routes::get(['url' => 'home', 'controller' => TeacherController::class, 'method' => 'loadLogin']);
        Routes::get(['url' => 'dashboard', 'controller' => TeacherController::class, 'method' => 'loadDashboard']);
        Routes::get(['url' => 'editStudent', 'controller' => TeacherController::class, 'method' => 'loadEditStudent']);
        Routes::get(['url' => 'teacherLogout', 'controller' => TeacherController::class, 'method' => 'loadLogin']);
        Routes::get(['url' => 'deleteStudent', 'controller' => TeacherController::class, 'method' => 'deleteStudent']);

        Routes::get(['url' => 'admin', 'controller' => AdminController::class, 'method' => 'loadLogin']);
        Routes::get(['url' => 'list', 'controller' => AdminController::class, 'method' => 'loadDashboard']);
        Routes::get(['url' => 'addSubject', 'controller' => AdminController::class, 'method' => 'loadSubjectForm']);
        Routes::get(['url' => 'addTeacher', 'controller' => AdminController::class, 'method' => 'loadTeacherForm']);
        Routes::get(['url' => 'logout', 'controller' => AdminController::class, 'method' => 'loadLogin']);

        Routes::post(['url' => 'home', 'controller' => TeacherController::class, 'method' => 'signIn']);
        Routes::post(['url' => 'addStudent', 'controller' => TeacherController::class, 'method' => 'addStudent']);
        Routes::post(['url' => 'editStudent', 'controller' => TeacherController::class, 'method' => 'updateStudent']);

        Routes::post(['url' => 'admin', 'controller' => AdminController::class, 'method' => 'signIn']);
        Routes::post(['url' => 'addSubject', 'controller' => AdminController::class, 'method' => 'addSubject']);
        Routes::post(['url' => 'addTeacher', 'controller' => AdminController::class, 'method' => 'addTeacher']);
    }
}
