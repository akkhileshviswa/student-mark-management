<?php

class AdminLogin
{
    use App\View\FormTrait;
}

$loginForm = new AdminLogin();
$loginForm->renderHeader();
?>

<div class="login-container">
    <form class="login-form" action="admin" method="POST" onsubmit="return loginvalidate();"
        autocomplete="off">
        <h2>Admin Login</h2>

<?php
$loginForm->renderLoginForm();
