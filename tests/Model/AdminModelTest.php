<?php

use PHPUnit\Framework\TestCase;
use App\Model\AdminModel;
use App\Core\Database;
use App\Core\Constants;

class AdminModelTest extends TestCase
{
    /** @var TeacherModel */
    private $teacherModel;

    /** @var Database */
    private $mockDatabase;

    /** @var PDOStatement */
    private $mockStatement;

    /**
     * Setup
     */
    protected function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = Constants::HTTP_POST;
        $this->mockDatabase = $this->createMock(Database::class);
        $this->adminModel = new AdminModel($this->mockDatabase);

        $mockConnection = $this->createMock(PDO::class);
        $this->mockDatabase->method('getConnection')->willReturn($mockConnection);
        $this->mockStatement = $this->createMock(PDOStatement::class);
        $mockConnection->method('prepare')->willReturn($this->mockStatement);
        $this->mockStatement->method('execute')->willReturn(true);
    }

    public function testSignInSuccessful()
    {
        $_POST['username'] = 'user';
        $_POST['password'] = 'pass';
        $this->mockStatement->method('fetch')->willReturn([
            Constants::COLUMN_USERNAME => 'user',
            Constants::COLUMN_PASSWORD => 'pass'
        ]);

        $result = $this->adminModel->signIn();
        $this->assertTrue($result);
    }

    public function testSignInFailed()
    {
        $_POST['username'] = 'testUser';
        $_POST['password'] = 'test';
        $this->mockStatement->method('fetch')->willReturn(false);

        $result = $this->adminModel->signIn();
        $this->assertFalse($result);
    }

    public function testLoadAdminDashboard()
    {
        $this->mockStatement->method('fetchAll')->willReturn([
            ['subject_name' => 'Math'],
            ['subject_name' => 'Science'],
        ]);

        $this->adminModel->loadAdminDashboard();
        $this->assertNotEmpty($_SESSION['subjects']);
    }

    public function testLoadAdminDashboardFailure()
    {
        $this->mockStatement->method('fetchAll')->willReturn([]);

        $this->adminModel->loadAdminDashboard();
        $this->assertEmpty($_SESSION['subjects']);
    }

    public function testAddSubjectSuccessful()
    {
        $_POST['subject_code'] = 'MATH';
        $_POST['subject_name'] = 'Maths';
        $this->mockStatement->method('fetch')->willReturn(false);

        $result = $this->adminModel->addSubject();
        $this->assertFalse($result);
    }

    public function testAddSubjectFailure()
    {
        $_POST['subject_code'] = 'TEST';
        $_POST['subject_name'] = 'Maths';
        $this->mockStatement->method('fetch')->willReturn([
            Constants::COLUMN_SUBJECT_CODE => 'TEST'
        ]);

        $result = $this->adminModel->addSubject();
        $this->assertEquals(Constants::EXCEPTION_UNIQUE, $result);
    }

    public function testGetSubjectName()
    {
        $subjectCode = 'MATH';
        $this->mockStatement->method('fetch')->willReturn([
            Constants::COLUMN_SUBJECT_NAME => 'Maths'
        ]);

        $result = $this->adminModel->getSubjectName($subjectCode);
        $this->assertEquals('Maths', $result);
    }

    public function testGetSubjectNameFailure()
    {
        $subjectCode = 'MATH';
        $this->mockStatement->method('fetch')->willReturn(false);

        $result = $this->adminModel->getSubjectName($subjectCode);
        $this->assertEmpty($result);
    }

    public function testAddTeacherSuccessful()
    {
        $_POST['name'] = 'test';
        $_POST['username'] = 'testuser';
        $_POST['password'] = 'test';
        $_POST['subject_code'] = ['MATH'];
        $this->mockStatement->method('fetch')->willReturn(false);

        $result = $this->adminModel->addTeacher();
        $this->assertFalse($result);
    }

    public function testAddTeacherFailure()
    {
        $_POST['name'] = 'test';
        $_POST['username'] = 'test';
        $_POST['password'] = 'test';
        $_POST['subject_code'] = ['MATH'];
        $this->mockStatement->method('fetch')->willReturn([
            Constants::COLUMN_USERNAME => 'test'
        ]);

        $result = $this->adminModel->addTeacher();
        $this->assertEquals(Constants::EXCEPTION_UNIQUE, $result);
    }
}
