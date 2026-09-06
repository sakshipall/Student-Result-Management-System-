<?php
require 'db.php';
require 'includes/functions.php';

$countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM students");
$countRow = mysqli_fetch_assoc($countResult);
$totalStudents = $countRow['total'];

$markCountResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM marks");
$markCountRow = mysqli_fetch_assoc($markCountResult);
$totalMarksEntered = $markCountRow['total'];

require 'includes/header.php';
?>

<section class="card welcome-card">
    <h2>Welcome</h2>
    <p>This system allows the college to register students, enter subject-wise marks, and automatically
       generate the result including total marks, percentage, grade and pass/fail status.</p>
</section>

<section class="stats-grid">
    <div class="stat-box">
        <h3><?php echo (int) $totalStudents; ?></h3>
        <p>Registered Students</p>
    </div>
    <div class="stat-box">
        <h3><?php echo (int) $totalMarksEntered; ?></h3>
        <p>Results Generated</p>
    </div>
</section>

<section class="card">
    <h2>Quick Search</h2>
    <form action="search_result.php" method="post" class="inline-form">
        <label for="roll_no">Roll Number</label>
        <input type="text" id="roll_no" name="roll_no" placeholder="Enter Roll Number" required>
        <button type="submit" class="btn">View Result</button>
    </form>
</section>

<section class="card">
    <h2>Modules</h2>
    <ul class="module-list">
        <li><a href="add_student.php">Add new student</a></li>
        <li><a href="students.php">View / manage all students, enter marks, edit or delete</a></li>
        <li><a href="search_result.php">Search result by roll number</a></li>
    </ul>
</section>

<?php require 'includes/footer.php'; ?>
