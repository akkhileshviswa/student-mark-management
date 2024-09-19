<?php

class TeacherLogin
{
    use App\View\FormTrait;
}

$loginForm = new TeacherLogin();
$loginForm->renderHeader();
?>
<div class="login-container">
    <form class="login-form" action="home" method="POST" onsubmit="return loginvalidate();" 
        autocomplete="off">
        <h2>Teacher Login</h2>

<?php
$loginForm->renderLoginForm();
