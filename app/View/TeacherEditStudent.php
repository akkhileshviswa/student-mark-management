<?php
use App\Core\Routes;

class TeacherEditStudent
{
    use App\View\FormTrait;
}

$dashboard = new TeacherEditStudent();
$dashboard->renderDashboardHeader();
?>

    <div class="home-form-container">
        <a href="dashboard" class="head-home">HOME</a>
        <a href="teacherLogout" class="head-home">LOGOUT</a>
    </div>
    <?php if (empty($_SESSION['update_student'])) {
        Routes::load('TeacherDashboard');
    }
$student = $_SESSION['update_student']; ?>
    <form id="forms" method="POST" action="editStudent">
        <h2>Edit Student</h2>
        <input type="hidden" id="student_id" name="student_id" value="<?= $student['id'];?>" >
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required autocomplete=off
            value=<?= ucfirst(htmlspecialchars($student['name'])); ?> ><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required autocomplete=off
            value=<?= htmlspecialchars($student['email']); ?> ><br>

        <label for="subject">Subject:</label>
        <select name="subject_code" id="subject_code">
            <option value="<?= htmlspecialchars($student['subject_code']); ?>">
                <?= ucfirst($dashboard->getSubjectName($student['subject_code'])); ?>
            </option>
            <?php foreach ($_SESSION['teacher_subject_codes'] as $subject_code):
                if ($student['subject_code'] != $subject_code): ?>
                <option value="<?= htmlspecialchars($subject_code); ?>">
                    <?= ucfirst($dashboard->getSubjectName($subject_code)); ?>
                </option>
            <?php endif;
            endforeach; ?>
        </select><br>

        <label for="marks">Marks:</label>
        <input type="number" id="marks" name="marks" required autocomplete=off step="0.25" min="0" max="100"
            oninput="validateMark(this)" value=<?= htmlspecialchars($student['mark']); ?> >

        <button type="submit" class="btn-add">Save</button>
    </form>
    </div>
</div>
</body>
</html>
