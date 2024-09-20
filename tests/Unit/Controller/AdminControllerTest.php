<?php

use PHPUnit\Framework\TestCase;
use App\Controller\AdminController;
use App\Model\AdminModel;
use App\Core\Constants;

class AdminControllerTest extends TestCase
{
    /** @var AdminController */
    private $adminController;

    /** @var AdminModel */
    private $adminModelMock;

    /**
     * Setup
     */
    protected function setUp(): void
    {
        $_SESSION['error_message'] = null;
        $_SESSION['success_message'] = null;

        $this->adminModelMock = $this->createMock(AdminModel::class);

        $this->adminController = $this->getMockBuilder(AdminController::class)
            ->setConstructorArgs([$this->adminModelMock])
            ->onlyMethods(['loadView', 'clearMessages'])
            ->getMock();
    }

    public function testLoadLogin()
    {
        $this->adminController->expects($this->once())
            ->method('loadView')
            ->with('AdminLogin');

        $this->adminController->loadLogin();
        $this->assertEquals(0, $_SESSION['adminloggedin']);
    }

    /**
     * @dataProvider signInProvider
     */
    public function testSignIn($signInResult, $loggedIn, $expectedErrorMessage)
    {
        $_SESSION['adminloggedin'] = 0;
        $this->adminModelMock->method('signIn')->willReturn($signInResult);

        $this->adminController->signIn();
        $this->assertEquals($loggedIn, $_SESSION['adminloggedin']);

        if ($signInResult) {
            $this->assertEquals(null, $_SESSION['error_message']);
        } else {
            $this->assertEquals($expectedErrorMessage, $_SESSION['error_message']);
        }
    }

    public function signInProvider()
    {
        return [
            'login success' => [true, 1, null],
            'login failure' => [false, 0, 'Username or Password is incorrect!!']
        ];
    }

    public function testLoadDashboardLoggedIn()
    {
        $_SESSION['adminloggedin'] = 1;
        $this->adminController->expects($this->once())
            ->method('loadView')
            ->with('AdminDashboard');
        $this->adminModelMock->expects($this->once())->method('loadAdminDashboard');

        $result = $this->adminController->loadDashboard();
        $this->assertEmpty($result);
    }

    public function testLoadDashboardNotLoggedIn()
    {
        $_SESSION['adminloggedin'] = 0;
        $this->adminController->expects($this->once())
            ->method('loadView')
            ->with('AdminLogin');

        $this->adminController->loadDashboard();
        $this->assertEquals('Login to continue!!', $_SESSION['error_message']);
    }

    public function testLoadSubjectFormLoggedIn()
    {
        $_SESSION['adminloggedin'] = 1;

        $this->adminController->loadSubjectForm();
        $this->assertEquals(1, $_SESSION['addSubject']);
        $this->assertEquals(0, $_SESSION['addTeacher']);
    }

    public function testLoadTeacherFormLoggedIn()
    {
        $_SESSION['adminloggedin'] = 1;

        $this->adminController->loadTeacherForm();
        $this->assertEquals(1, $_SESSION['addTeacher']);
        $this->assertEquals(0, $_SESSION['addSubject']);
    }

    /**
     * @dataProvider addTeacherProvider
     */
    public function testAddTeacher($result, $expectedError, $expectedSuccess)
    {
        $this->adminModelMock->method('addTeacher')->willReturn($result);
        $this->adminController->addTeacher();

        if ($expectedError) {
            $this->assertEquals($expectedError, $_SESSION['error_message']);
        } else {
            $this->assertEquals($expectedSuccess, $_SESSION['success_message']);
        }
    }

    public function addTeacherProvider()
    {
        return [
            'unique constraint violation' => [Constants::EXCEPTION_UNIQUE, 'Username should be Unique!', null],
            'name length violation' => [Constants::EXCEPTION_NAME_LENGTH, 'Username should be greater than 4 characters!', null],
            'success' => [null, null, 'Teacher has been created successfully']
        ];
    }

    /**
     * @dataProvider addSubjectProvider
     */
    public function testAddSubject($result, $expectedError, $expectedSuccess)
    {
        $this->adminModelMock->method('addSubject')->willReturn($result);
        $this->adminController->addSubject();

        if ($expectedError) {
            $this->assertEquals($expectedError, $_SESSION['error_message']);
        } else {
            $this->assertEquals($expectedSuccess, $_SESSION['success_message']);
        }
    }

    public function addSubjectProvider()
    {
        return [
            'unique constraint violation' => [Constants::EXCEPTION_UNIQUE, 'Subject Code should be Unique!', null],
            'name length violation' => [Constants::EXCEPTION_NAME_LENGTH, 'Subject Name should be greater than 4 characters!', null],
            'success' => [null, null, 'Subject has been created successfully']
        ];
    }
}
