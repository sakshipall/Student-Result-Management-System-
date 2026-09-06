<?php
require 'db.php';
require 'includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);

if ($id <= 0) {
    setFlash('error', 'Invalid student selected.');
    header('Location: students.php');
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$student) {
    setFlash('error', 'Student not found.');
    header('Location: students.php');
    exit;
}

$errors = [];
$name          = $student['name'];
$roll_no       = $student['roll_no'];
$enrollment_no = $student['enrollment_no'];
$course        = $student['course'];
$semester      = $student['semester'];
$email         = $student['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = clean($_POST['name'] ?? '');
    $roll_no       = clean($_POST['roll_no'] ?? '');
    $enrollment_no = clean($_POST['enrollment_no'] ?? '');
    $course        = clean($_POST['course'] ?? '');
    $semester      = clean($_POST['semester'] ?? '');
    $email         = clean($_POST['email'] ?? '');

    if ($name === '')          $errors[] = 'Student name is required.';
    if ($roll_no === '')       $errors[] = 'Roll number is required.';
    if ($enrollment_no === '') $errors[] = 'Enrollment number is required.';
    if ($course === '')        $errors[] = 'Course is required.';
    if ($semester === '')      $errors[] = 'Semester is required.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email format is invalid.';
    }

    // Roll number must stay unique (excluding this student's own record)
    if (empty($errors)) {
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM students WHERE roll_no = ? AND id != ?");
        mysqli_stmt_bind_param($checkStmt, 'si', $roll_no, $id);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $errors[] = 'Roll number already used by another student.';
        }
        mysqli_stmt_close($checkStmt);
    }

    if (empty($errors)) {
        $updateStmt = mysqli_prepare(
            $conn,
            "UPDATE students SET roll_no=?, enrollment_no=?, name=?, course=?, semester=?, email=? WHERE id=?"
        );
        mysqli_stmt_bind_param($updateStmt, 'ssssssi', $roll_no, $enrollment_no, $name, $course, $semester, $email, $id);

        if (mysqli_stmt_execute($updateStmt)) {
            mysqli_stmt_close($updateStmt);
            setFlash('success', 'Student details updated successfully.');
            header('Location: students.php');
            exit;
        } else {
            $errors[] = 'Could not update student. Please try again.';
        }
        mysqli_stmt_close($updateStmt);
    }
}

require 'includes/header.php';
?>

<section class="card">
    <h2>Edit Student</h2>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
            <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="edit_student.php?id=<?php echo $id; ?>" method="post" class="form-grid">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="name">Student Name *</label>
            <input type="text" id="name" name="name" value="<?php echo $name; ?>" required>
        </div>
        <div class="form-group">
            <label for="roll_no">Roll Number *</label>
            <input type="text" id="roll_no" name="roll_no" value="<?php echo $roll_no; ?>" required>
        </div>
        <div class="form-group">
            <label for="enrollment_no">Enrollment Number *</label>
            <input type="text" id="enrollment_no" name="enrollment_no" value="<?php echo $enrollment_no; ?>" required>
        </div>
        <div class="form-group">
            <label for="course">Course *</label>
            <input type="text" id="course" name="course" value="<?php echo $course; ?>" required>
        </div>
        <div class="form-group">
            <label for="semester">Semester *</label>
            <input type="text" id="semester" name="semester" value="<?php echo $semester; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email (optional)</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn">Update Student</button>
            <a href="students.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</section>

<?php require 'includes/footer.php'; ?>
