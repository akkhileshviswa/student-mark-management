<?php
class AdminDashboard
{
    use App\View\FormTrait;
}

$dashboard = new AdminDashboard();
$dashboard->renderDashboardHeader();
?>
    <div class="home-container">
        <div class="message-container">
            <?php $dashboard->getMessages(); ?>
        </div>
        <div class="button-container">
            <a href="list" class="head-home">HOME</a>
            <a href="logout" class="head-home">LOGOUT</a>
        </div>
    </div>
    <h2>Subject and Teacher Management</h2>
    <h3>Subjects</h3>
    <table>
        <tr>
            <th>Subject Name</th>
            <th>Subject Code</th>
        </tr>
        <?php if (! empty($_SESSION['subjects'])): ?>
            <?php foreach ($_SESSION['subjects'] as $subject): ?>
                <tr>
                    <td><?= ucfirst(htmlspecialchars($subject['subject_name'])); ?></td>
                    <td><?= htmlspecialchars($subject['subject_code']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No subjects added yet.</td></tr>
        <?php endif; ?>
    </table>
    <form method="GET" action="addSubject">
        <input type="submit" class="btn" value="Add Subject">
    </form>

    <h3>Teachers</h3>
    <table>
        <tr>
            <th>Teacher Name</th>
            <th>Teacher Username</th>
            <th>Subjects</th>
        </tr>
        <?php if (! empty($_SESSION['teachers'])): ?>
            <?php foreach ($_SESSION['teachers'] as $teacher): ?>
                <tr>
                    <td><?= ucfirst(htmlspecialchars($teacher['name'])); ?></td>
                    <td><?= htmlspecialchars($teacher['username']); ?></td>
                    <td>
                        <?php foreach (json_decode($teacher['subject_code']) as $code): ?>
                        <?= ucfirst($dashboard->getSubjectName($code)); ?>
                        <?php endforeach; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php elseif (empty($_SESSION['subjects'])): ?>
            <tr><td colspan="3">Add subject for adding a teacher.</td></tr>
        <?php else: ?>
            <tr><td colspan="3">No teachers added yet.</td></tr>
        <?php endif; ?>
    </table>
    <?php if (! empty($_SESSION['subjects'])): ?>
        <form method="GET" action="addTeacher">
            <input type="submit" class="btn" value="Add Teacher">
        </form>
    <?php endif; ?>
</div>
</body>
</html>
