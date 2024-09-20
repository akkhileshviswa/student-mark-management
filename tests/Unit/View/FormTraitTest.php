<?php

use PHPUnit\Framework\TestCase;
use App\View\FormTrait;
use App\Model\AdminModel;

class FormTraitTest extends TestCase
{
    /** @var FormTrait */
    private $formTrait;

    /**
     * Setup
     */
    protected function setUp(): void
    {
        $this->formTrait = new class () {
            use FormTrait;
        };
    }

    public function testRenderHeader()
    {
        ob_start();
        $this->formTrait->renderHeader();
        $output = ob_get_clean();

        $this->assertStringContainsString('<title>Login</title>', $output);
    }

    public function testRenderLoginForm()
    {
        ob_start();
        $this->formTrait->renderLoginForm();
        $output = ob_get_clean();

        $this->assertStringContainsString('<label for="username">Username</label>', $output);
        $this->assertStringContainsString('<input type="text" id="username"', $output);
    }

    public function testRenderDashboardHeader()
    {
        ob_start();
        $this->formTrait->renderDashboardHeader();
        $output = ob_get_clean();

        $this->assertStringContainsString('<title>Student Management System</title>', $output);
    }

    public function testGetMessages()
    {
        $_SESSION['success_message'] = 'Test';
        ob_start();
        $this->formTrait->getMessages();
        $output = ob_get_clean();

        $this->assertStringContainsString('<span id="success_message">', $output);
    }
}
