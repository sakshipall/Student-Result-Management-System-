<?php
require 'db.php';
require 'includes/functions.php';

$errors = [];
$name = $roll_no = $enrollment_no = $course = $semester = $email = '';

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

    // Check roll number uniqueness
    if (empty($errors)) {
        $checkStmt = mysqli_prepare($conn, "SELECT id FROM students WHERE roll_no = ?");
        mysqli_stmt_bind_param($checkStmt, 's', $roll_no);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            $errors[] = 'Roll number already exists. Please use a different roll number.';
        }
        mysqli_stmt_close($checkStmt);
    }

    if (empty($errors)) {
        $insertStmt = mysqli_prepare(
            $conn,
            "INSERT INTO students (roll_no, enrollment_no, name, course, semester, email) VALUES (?, ?, ?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($insertStmt, 'ssssss', $roll_no, $enrollment_no, $name, $course, $semester, $email);

        if (mysqli_stmt_execute($insertStmt)) {
            mysqli_stmt_close($insertStmt);
            setFlash('success', 'Student "' . $name . '" added successfully.');
            header('Location: students.php');
            exit;
        } else {
            $errors[] = 'Something went wrong while saving. Please try again.';
        }
        mysqli_stmt_close($insertStmt);
    }
}

require 'includes/header.php';
?>

<section class="card">
    <h2>Add New Student</h2>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
            <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="add_student.php" method="post" class="form-grid">
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
            <input type="text" id="course" name="course" value="<?php echo $course !== '' ? $course : 'B.Sc IT (Hons)'; ?>" required>
        </div>
        <div class="form-group">
            <label for="semester">Semester *</label>
            <input type="text" id="semester" name="semester" value="<?php echo $semester !== '' ? $semester : 'VII'; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email (optional)</label>
            <input type="email" id="email" name="email" value="<?php echo $email; ?>">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn">Add Student</button>
        </div>
    </form>
</section>

<?php require 'includes/footer.php'; ?>
