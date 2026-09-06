<?php $flash = getFlash(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Result Management System</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <div class="brand">
            <h1>Student Result Management System</h1>
            <p class="subtitle">B.Sc IT (Honours) &middot; Semester VII &middot; CS404PHP</p>
        </div>
        <nav class="main-nav">
            <a href="index.php">Home</a>
            <a href="add_student.php">Add Student</a>
            <a href="students.php">All Students</a>
            <a href="search_result.php">Search Result</a>
        </nav>
    </div>
</header>
<main class="container">
    <?php if ($flash): ?>
    <div class="alert alert-<?php echo clean($flash['type']); ?>">
        <?php echo clean($flash['message']); ?>
    </div>
    <?php endif; ?>
