<?php

use PHPUnit\Framework\TestCase;
use App\Controller\TeacherController;
use App\Model\TeacherModel;
use App\Model\AdminModel;
use App\Core\Constants;

class TeacherControllerTest extends TestCase
{
    /** @var TeacherController */
    private $teacherController;

    /** @var TeacherModel */
    private $teacherModelMock;

    /** @var AdminModel */
    private $adminModelMock;

    /**
     * Setup
     */
    protected function setUp(): void
    {
        $this->teacherModelMock = $this->createMock(TeacherModel::class);
        $this->adminModelMock = $this->createMock(AdminModel::class);

        $this->teacherController = $this->getMockBuilder(TeacherController::class)
            ->setConstructorArgs([$this->adminModelMock, $this->teacherModelMock])
            ->onlyMethods(['loadView'])
            ->getMock();
    }

    public function testLoadLogin()
    {
        $this->teacherController->expects($this->once())
            ->method('loadView')
            ->with('TeacherLogin');

        $this->teacherController->loadLogin();
        $this->assertEquals(0, $_SESSION['teacherloggedin']);
    }

    /**
     * @dataProvider signInProvider
     */
    public function testSignIn($signInResult, $loggedIn, $expectedRoute)
    {
        $_SESSION['error_message'] = null;
        $_SESSION['teacherloggedin'] = 0;
        $this->teacherModelMock->method('signIn')->willReturn($signInResult);

        $this->teacherController->signIn();
        $this->assertEquals($loggedIn, $_SESSION['teacherloggedin']);

        if ($signInResult) {
            $this->assertNull($_SESSION['error_message']);
        } else {
            $this->assertEquals('Username or Password is incorrect!!', $_SESSION['error_message']);
        }
    }

    public function signInProvider()
    {
        return [
            'login success' => [true, 1, 'TeacherDashboard'],
            'login failure' => [false, 0, 'TeacherLogin']
        ];
    }

    public function testLoadDashboardLoggedIn()
    {
        $_SESSION['teacherloggedin'] = 1;
        $this->teacherController->expects($this->once())
            ->method('loadView')
            ->with('TeacherDashboard');
        $this->teacherModelMock->expects($this->once())->method('loadDashboard');

        $result = $this->teacherController->loadDashboard();
        $this->assertEmpty($result);
    }

    public function testLoadDashboardNotLoggedIn()
    {
        $_SESSION['teacherloggedin'] = 0;
        $this->teacherController->expects($this->once())
            ->method('loadView')
            ->with('TeacherLogin');

        $this->teacherController->loadDashboard();
        $this->assertEquals('Login to continue', $_SESSION['error_message']);
    }

    public function testLoadEditStudentLoggedIn()
    {
        $_SESSION['teacherloggedin'] = 1;
        $_GET['student_id'] = 1;
        $student = (object) ['id' => 1, 'name' => 'Test'];

        $this->teacherModelMock->method('getStudentById')->willReturn($student);
        $this->teacherController->expects($this->once())
            ->method('loadView')
            ->with('TeacherEditStudent');

        $this->teacherController->loadEditStudent();
        $this->assertEquals($student, $_SESSION['update_student']);
    }

    public function testLoadEditStudentNotLoggedIn()
    {
        $_SESSION['teacherloggedin'] = 0;
        $this->teacherController->expects($this->once())
            ->method('loadView')
            ->with('TeacherLogin');

        $this->teacherController->loadEditStudent();
        $this->assertEquals('Login to continue', $_SESSION['error_message']);
    }

    /**
     * @dataProvider addStudentProvider
     */
    public function testAddStudent($emailValid, $expectedSuccess, $expectedError)
    {
        $_SESSION['teacherloggedin'] = 1;
        $_SERVER['REQUEST_METHOD'] = Constants::HTTP_POST;
        $_POST['name'] = 'Test';
        $_POST['email'] = $emailValid ? 'test@sample.com' : 'invalid-email';
        $_POST['marks'] = 85;
        $_POST['subject_code'] = 'MATH';

        $this->teacherModelMock->method('addStudent')->willReturn(
            [true, (object)['id' => 1, 'name' => 'Test', 'subject_code' => 'MATH']]
        );
        $this->adminModelMock->method('getSubjectName')->willReturn('Maths');

        ob_start();
        $this->teacherController->addStudent();
        $output = ob_get_clean();

        $response = json_decode($output, true);
        if ($emailValid) {
            $this->assertTrue($response['success']);
            $this->assertEquals('Maths', $response['student']['subject_name']);
        } else {
            $this->assertFalse($response['success']);
            $this->assertEquals($expectedError, $response['error']);
        }
    }

    public function addStudentProvider()
    {
        return [
            'valid email' => [true, true, null],
            'invalid email' => [false, false, 'Enter valid email!']
        ];
    }

    public function testDeleteStudentLoggedIn()
    {
        $_SESSION['teacherloggedin'] = 1;
        $_SERVER['REQUEST_METHOD'] = Constants::HTTP_GET;
        $_GET['id'] = 1;
        $this->teacherModelMock->expects($this->once())
            ->method('deleteStudent')
            ->with(1);
        $this->teacherController->expects($this->once())
            ->method('loadView')
            ->with('TeacherDashboard');

        $this->teacherController->deleteStudent();
    }

    public function testDeleteStudentNotLoggedIn()
    {
        $_SESSION['teacherloggedin'] = 0;
        $this->teacherController->expects($this->once())
            ->method('loadView')
            ->with('TeacherLogin');

        $this->teacherController->deleteStudent();
        $this->assertEquals('Login to continue', $_SESSION['error_message']);
    }
}
