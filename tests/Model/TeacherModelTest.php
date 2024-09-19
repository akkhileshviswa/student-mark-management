<?php

use PHPUnit\Framework\TestCase;
use App\Model\TeacherModel;
use App\Core\Database;
use App\Core\Constants;

class TeacherModelTest extends TestCase
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
        $this->teacherModel = new TeacherModel($this->mockDatabase);

        $this->mockStatement = $this->createMock(PDOStatement::class);
        $mockConnection = $this->createMock(PDO::class);
        $this->mockDatabase->method('getConnection')->willReturn($mockConnection);
        $mockConnection->method('prepare')->willReturn($this->mockStatement);
    }

    public function testSignInSuccess()
    {
        $_POST['username'] = 'test';
        $_POST['password'] = 'test';
        $this->mockStatement->method('execute')->willReturn(true);
        $this->mockStatement->method('fetch')->willReturn([
            Constants::COLUMN_USERNAME => 'test',
            Constants::COLUMN_PASSWORD => md5('test'),
            Constants::COLUMN_ID => 1,
            Constants::COLUMN_NAME => 'Test',
            Constants::COLUMN_SUBJECT_CODE => json_encode(['MATH']),
        ]);

        $result = $this->teacherModel->signIn();
        $this->assertTrue($result);
        $this->assertEquals($_SESSION['teacher_id'], 1);
        $this->assertEquals($_SESSION['teacher_name'], 'Test');
    }

    public function testSignInFailed()
    {
        $_POST['username'] = 'test';
        $_POST['password'] = 'admin';
        $this->mockStatement->method('execute')->willReturn(true);
        $this->mockStatement->method('fetch')->willReturn(false);

        $result = $this->teacherModel->signIn();
        $this->assertFalse($result);
    }

    public function testLoadDashboard()
    {
        $_SESSION['teacher_id'] = 1;
        $this->mockStatement->method('execute')->willReturn(true);
        $this->mockStatement->method('fetchAll')->willReturn([
            ['name' => 'Test 1', 'email' => 'test1@gh.com'],
            ['name' => 'Test 2', 'email' => 'test2@df.com'],
        ]);

        $this->teacherModel->loadDashboard();
        $this->assertNotEmpty($_SESSION['students']);
    }

    /**
     * @dataProvider studentIdProvider
     */
    public function testGetStudentByIdSuccess($studentId, $expectedName)
    {
        $this->mockStatement->method('execute')->willReturn(true);
        $this->mockStatement->method('fetch')->willReturn([
            Constants::COLUMN_ID => $studentId,
            Constants::COLUMN_NAME => $expectedName,
            Constants::COLUMN_EMAIL => 'test@test.com',
        ]);

        $result = $this->teacherModel->getStudentById($studentId);
        $this->assertEquals($expectedName, $result[Constants::COLUMN_NAME]);
    }

    public function studentIdProvider()
    {
        return [
            [1, 'Student 1'],
            [2, 'Student 2'],
            [3, 'Student 3'],
        ];
    }

    public function testGetStudentByIdFailure()
    {
        $studentId = 99;
        $this->mockStatement->method('execute')->willReturn(true);
        $this->mockStatement->method('fetch')->willReturn(false);

        $result = $this->teacherModel->getStudentById($studentId);
        $this->assertFalse($result);
    }

    public function testUpdateStudentMarkSuccess()
    {
        $email = 'test@test.com';
        $mark = 85;
        $subjectCode = 'ENG';
        $teacherId = 1;
        $this->mockStatement->method('execute')->willReturn(true);

        $result = $this->teacherModel->updateStudentMark($email, $mark, $subjectCode, $teacherId);
        $this->assertNull($result);
    }

    public function testUpdateStudentMarkFailure()
    {
        $email = 'test@test.df';
        $mark = 85;
        $subjectCode = 'SCI';
        $teacherId = 1;
        $this->mockStatement->method('execute')->willThrowException(new Exception("Database error"));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Database error");

        $this->teacherModel->updateStudentMark($email, $mark, $subjectCode, $teacherId);
    }

    public function testAddStudent()
    {
        $name = 'JOHN';
        $email = 'john@gh.com';
        $mark = 2;
        $subjectCode = 'eng';
        $this->mockStatement->method('execute')->willReturn(true);
        $this->mockStatement->method('fetch')->willReturn(false);

        $result = $this->teacherModel->addStudent($name, $email, $mark, $subjectCode);
        $this->assertTrue($result[0]);
    }

    public function testAddStudentFailure()
    {
        $name = 'JOHN';
        $email = 'john@gamil.com';
        $mark = 99;
        $subjectCode = 'SOCIAL';
        $this->mockStatement->method('execute')->willThrowException(new Exception("Unexpected error"));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unexpected error");

        $this->teacherModel->addStudent($name, $email, $mark, $subjectCode);
    }

    public function testDeleteStudent()
    {
        $id = 1;
        $this->mockStatement->method('execute')->willReturn(true);

        $result = $this->teacherModel->deleteStudent($id);
        $this->assertTrue($result);
    }

    public function testDeleteStudentFailure()
    {
        $id = 99;
        $this->mockStatement->method('execute')->willThrowException(new Exception("ID not found"));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("ID not found");

        $this->teacherModel->deleteStudent($id);
    }
}
