<?php
require 'db.php';
require 'includes/functions.php';

$rollNo = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rollNo = clean($_POST['roll_no'] ?? '');

    if ($rollNo === '') {
        $error = 'Please enter a roll number to search.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM students WHERE roll_no = ?");
        mysqli_stmt_bind_param($stmt, 's', $rollNo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $studentId);

        if (mysqli_stmt_fetch($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: result.php?id=' . (int) $studentId);
            exit;
        }

        mysqli_stmt_close($stmt);
        $error = 'No student found with roll number "' . $rollNo . '".';
    }
}

require 'includes/header.php';
?>

<section class="card">
    <h2>Search Result</h2>
    <p>Enter the student's roll number to view their result.</p>

    <?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="search_result.php" method="post" class="inline-form">
        <label for="roll_no">Roll Number</label>
        <input type="text" id="roll_no" name="roll_no" value="<?php echo $rollNo; ?>" required>
        <button type="submit" class="btn">Search</button>
    </form>
</section>

<?php require 'includes/footer.php'; ?>
