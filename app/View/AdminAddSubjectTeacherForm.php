<?php
class AdminDashboard
{
    use App\View\FormTrait;
}

$dashboard = new AdminDashboard();
$dashboard->renderDashboardHeader();
?>

    <div class="home-form-container">
        <a href="list" class="head-home">HOME</a>
        <a href="logout" class="head-home">LOGOUT</a>
    </div>
    <?php if ($_SESSION['addSubject']): ?>
        <form method="POST" action="addSubject" id="forms">
            <h2>Add Subject</h2>
            <label for="subject_name">Subject Name:</label>
            <input type="text" name="subject_name" id="subject_name" required autocomplete=off
                value=<?= (isset($_POST['subject_name']) ? htmlspecialchars($_POST['subject_name']) : '')?> >

            <br><label for="subject_code">Subject Code:</label>
            <input type="text" name="subject_code" id="subject_code" required autocomplete=off
                value=<?= (isset($_POST['subject_code']) ? htmlspecialchars($_POST['subject_code']) : '')?> >

            <br><input type="submit" class="btn" value="Add Subject">
        </form>
    <?php endif; ?>

    <?php if ($_SESSION['addTeacher']): ?>
        <form method="POST" action="addTeacher" id="forms">
            <h2>Add Teacher</h2>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required autocomplete=off
                value=<?= (isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '')?> ><br>

            <label for="teacher_username">Username:</label>
            <input type="text" name="username" id="username" required autocomplete=off
                value=<?= (isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '')?> ><br>

            <label for="teacher_password">Password:</label>
            <input type="password" name="password" id="password" autocomplete=off
                value=<?= (isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '')?> ><br>

            <label for="subject_code">Subjects:</label>
            <select name="subject_code[]" id="admin_subject_code" required multiple>
                <?php foreach ($_SESSION['subjects'] as $subject): ?>
                <option value="<?= htmlspecialchars($subject['subject_code']); ?>">
                    <?= htmlspecialchars($subject['subject_name']); ?>
                </option>
                <?php endforeach; ?>
            </select><br>
            <input type="submit" class="btn" value="Add Teacher">
        </form>
    <?php endif; ?>
</div>
</body>
</html>
