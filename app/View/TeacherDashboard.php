<?php
class TeacherDashboard
{
    use App\View\FormTrait;
}

$dashboard = new TeacherDashboard();
$dashboard->renderDashboardHeader();
?>

    <div class="home-container">
        <div class="message-container">
            <?php $dashboard->getMessages(); ?>
        </div>
        <div class="button-container">
            <a href="dashboard" class="head-home">HOME</a>
            <a href="teacherLogout" class="head-home">LOGOUT</a>
        </div>
    </div>
    <h3>Welcome <?= ucfirst($_SESSION['teacher_name']); ?></h3>
    <table id="studentTable">
        <tr>
            <th>Student Name</th>
            <th>Student Email</th>
            <th>Subject</th>
            <th>Marks</th>
            <th>Actions</th>
        </tr>
        <?php if (! empty($_SESSION['students'])): ?>
            <?php foreach ($_SESSION['students'] as $student): ?>
                <tr data-name="<?= htmlspecialchars($student['name']); ?>" 
                    data-email="<?= htmlspecialchars($student['email']); ?>" 
                    data-subject_code="<?= htmlspecialchars($student['subject_code']); ?>">
                    <td><?= ucfirst(htmlspecialchars($student['name'])); ?></td>
                    <td><?= htmlspecialchars($student['email']); ?></td>
                    <td><?= ucfirst($dashboard->getSubjectName($student['subject_code'])); ?></td>
                    <td class="student-marks"><?= htmlspecialchars($student['mark']); ?></td>
                    <td>
                        <form method="GET" action="edit-student">
                            <input type="hidden" id="student_id" name="student_id" value="<?= $student['id'];?>" >
                            <input type="submit" class="btn" value="Edit">
                        </form><br>
                        <a href="/delete-student?id=<?= htmlspecialchars($student['id']); ?>" id='delete' onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr id = 'no_data_message'><td colspan="5">No students data available yet.</td></tr>
        <?php endif; ?>
    </table>
    <button id="openPopup" class="btn-add">Add Student</button>
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close">&times;</span>
            <form id="studentForm">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required autocomplete=off value= ''><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required autocomplete=off value= '' ><br>

                <label for="subject">Subject:</label>
                <select name="subject_code" id="subject_code">
                    <?php foreach ($_SESSION['teacher_subject_codes'] as $subject_code): ?>
                    <option value="<?= htmlspecialchars($subject_code); ?>">
                        <?= ucfirst($dashboard->getSubjectName($subject_code)); ?>
                    </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="marks">Marks:</label>
                <input type="number" id="marks" name="marks" required autocomplete=off step="0.25" min="0" max="100"
                    oninput="validateMark(this)" value= '' >

                <button type="submit" class="btn-add">Save</button>
            </form>
        </div>
    </div>
    <script src="http://localhost/student-mark-management/assets/js/addStudent.js"></script>
</div>
</body>
</html>
