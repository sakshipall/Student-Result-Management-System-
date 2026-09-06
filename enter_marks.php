<?php
require 'db.php';
require 'includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['student_id']) ? (int) $_POST['student_id'] : 0);

if ($id <= 0) {
    setFlash('error', 'Invalid student selected.');
    header('Location: students.php');
    exit;
}

// Fetch the student
$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$studentResult = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($studentResult);
mysqli_stmt_close($stmt);

if (!$student) {
    setFlash('error', 'Student not found.');
    header('Location: students.php');
    exit;
}

// Fetch existing marks (if any) so the form can be pre-filled for editing
$markStmt = mysqli_prepare($conn, "SELECT * FROM marks WHERE student_id = ?");
mysqli_stmt_bind_param($markStmt, 'i', $id);
mysqli_stmt_execute($markStmt);
$markResult = mysqli_stmt_get_result($markStmt);
$existingMarks = mysqli_fetch_assoc($markResult);
mysqli_stmt_close($markStmt);

$subjectFields = $GLOBALS['subject_fields'];
$subjectLabels = $GLOBALS['subject_labels'];

$marksInput = [];
foreach ($subjectFields as $field) {
    $marksInput[$field] = $existingMarks[$field] ?? '';
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marksValues = [];

    // Loop through the 5 subjects and validate each one (0-100)
    foreach ($subjectFields as $index => $field) {
        $value = $_POST[$field] ?? '';
        $marksInput[$field] = $value;

        if ($value === '' || !is_numeric($value)) {
            $errors[] = $subjectLabels[$index] . ' marks are required and must be numeric.';
            continue;
        }

        $value = (int) $value;
        if ($value < 0 || $value > 100) {
            $errors[] = $subjectLabels[$index] . ' marks must be between 0 and 100.';
            continue;
        }

        $marksValues[$field] = $value;
    }

    if (empty($errors)) {
        $total      = array_sum($marksValues);
        $maxMarks   = 100 * count($subjectFields);
        $percentage = round(($total / $maxMarks) * 100, 2);
        $grade      = calculateGrade($percentage);
        $status     = calculateResultStatus(array_values($marksValues));

        if ($existingMarks) {
            // UPDATE existing marks row
            $updateStmt = mysqli_prepare(
                $conn,
                "UPDATE marks SET subject1=?, subject2=?, subject3=?, subject4=?, subject5=?, total=?, percentage=?, grade=?, result_status=? WHERE student_id=?"
            );
            mysqli_stmt_bind_param(
                $updateStmt,
                'iiiiiidssi',
                $marksValues['subject1'],
                $marksValues['subject2'],
                $marksValues['subject3'],
                $marksValues['subject4'],
                $marksValues['subject5'],
                $total,
                $percentage,
                $grade,
                $status,
                $id
            );
            $ok = mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt);
        } else {
            // INSERT new marks row
            $insertStmt = mysqli_prepare(
                $conn,
                "INSERT INTO marks (student_id, subject1, subject2, subject3, subject4, subject5, total, percentage, grade, result_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param(
                $insertStmt,
                'iiiiiiidss',
                $id,
                $marksValues['subject1'],
                $marksValues['subject2'],
                $marksValues['subject3'],
                $marksValues['subject4'],
                $marksValues['subject5'],
                $total,
                $percentage,
                $grade,
                $status
            );
            $ok = mysqli_stmt_execute($insertStmt);
            mysqli_stmt_close($insertStmt);
        }

        if ($ok) {
            setFlash('success', 'Marks saved successfully for ' . $student['name'] . '.');
            header('Location: result.php?id=' . $id);
            exit;
        } else {
            $errors[] = 'Could not save marks. Please try again.';
        }
    }
}

require 'includes/header.php';
?>

<section class="card">
    <h2><?php echo $existingMarks ? 'Edit Marks' : 'Enter Marks'; ?></h2>
    <p class="student-meta">
        <strong><?php echo clean($student['name']); ?></strong> &middot;
        Roll No: <?php echo clean($student['roll_no']); ?> &middot;
        <?php echo clean($student['course']); ?>, Semester <?php echo clean($student['semester']); ?>
    </p>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
            <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="enter_marks.php?id=<?php echo $id; ?>" method="post" class="form-grid" id="marksForm">
        <input type="hidden" name="student_id" value="<?php echo $id; ?>">
        <?php foreach ($subjectFields as $index => $field): ?>
        <div class="form-group">
            <label for="<?php echo $field; ?>"><?php echo $subjectLabels[$index]; ?> (out of 100) *</label>
            <input type="number" min="0" max="100" id="<?php echo $field; ?>" name="<?php echo $field; ?>"
                   value="<?php echo clean((string) $marksInput[$field]); ?>" required>
        </div>
        <?php endforeach; ?>
        <div class="form-actions">
            <button type="submit" class="btn">Save Marks</button>
            <a href="students.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<script>
// Basic JavaScript validation before the form is submitted to the server
document.getElementById('marksForm').addEventListener('submit', function (e) {
    var inputs = document.querySelectorAll('#marksForm input[type="number"]');
    for (var i = 0; i < inputs.length; i++) {
        var val = parseInt(inputs[i].value, 10);
        if (isNaN(val) || val < 0 || val > 100) {
            alert('Please enter marks between 0 and 100 for all subjects.');
            e.preventDefault();
            return;
        }
    }
});
</script>

<?php require 'includes/footer.php'; ?>
