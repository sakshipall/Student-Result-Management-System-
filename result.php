<?php
require 'db.php';
require 'includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$student = null;

if ($id > 0) {
    $stmt = mysqli_prepare(
        $conn,
        "SELECT s.*, m.subject1, m.subject2, m.subject3, m.subject4, m.subject5,
                m.total, m.percentage, m.grade, m.result_status
         FROM students s
         LEFT JOIN marks m ON m.student_id = s.id
         WHERE s.id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
}

$subjectFields = $GLOBALS['subject_fields'];
$subjectLabels = $GLOBALS['subject_labels'];

require 'includes/header.php';
?>

<section class="card">
<?php if (!$student): ?>
    <div class="alert alert-error">Student not found.</div>
    <a href="students.php" class="btn">Back to Students</a>
<?php elseif ($student['total'] === null): ?>
    <div class="alert alert-error">Marks have not been entered for this student yet.</div>
    <a href="enter_marks.php?id=<?php echo $id; ?>" class="btn">Enter Marks Now</a>
<?php else: ?>
    <div class="result-sheet">
        <div class="result-header">
            <h2>Student Result Sheet</h2>
            <p class="subtitle">B.Sc IT (Honours) &middot; Semester VII &middot; CS404PHP</p>
        </div>

        <table class="info-table">
            <tr><th>Student Name</th><td><?php echo clean($student['name']); ?></td></tr>
            <tr><th>Roll Number</th><td><?php echo clean($student['roll_no']); ?></td></tr>
            <tr><th>Enrollment Number</th><td><?php echo clean($student['enrollment_no']); ?></td></tr>
            <tr><th>Course</th><td><?php echo clean($student['course']); ?></td></tr>
            <tr><th>Semester</th><td><?php echo clean($student['semester']); ?></td></tr>
        </table>

        <table class="data-table marks-table">
            <thead>
                <tr><th>Subject</th><th>Marks Obtained</th><th>Max Marks</th></tr>
            </thead>
            <tbody>
            <?php foreach ($subjectFields as $index => $field): ?>
                <tr>
                    <td><?php echo $subjectLabels[$index]; ?></td>
                    <td><?php echo (int) $student[$field]; ?></td>
                    <td>100</td>
                </tr>
            <?php endforeach; ?>
                <tr class="total-row">
                    <td>Total</td>
                    <td><?php echo (int) $student['total']; ?></td>
                    <td>500</td>
                </tr>
            </tbody>
        </table>

        <table class="info-table">
            <tr><th>Percentage</th><td><?php echo number_format($student['percentage'], 2); ?>%</td></tr>
            <tr><th>Grade</th><td><?php echo clean($student['grade']); ?></td></tr>
            <tr>
                <th>Result</th>
                <td>
                    <span class="badge badge-<?php echo strtolower($student['result_status']); ?>">
                        <?php echo clean($student['result_status']); ?>
                    </span>
                </td>
            </tr>
        </table>

        <div class="result-actions no-print">
            <a href="enter_marks.php?id=<?php echo $id; ?>" class="btn">Edit Marks</a>
            <a href="students.php" class="btn btn-secondary">Back to Students</a>
            <button type="button" onclick="window.print()" class="btn btn-secondary">Print Result</button>
        </div>
    </div>
<?php endif; ?>
</section>

<?php require 'includes/footer.php'; ?>
