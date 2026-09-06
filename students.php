<?php
require 'db.php';
require 'includes/functions.php';

$query = "SELECT s.id, s.roll_no, s.enrollment_no, s.name, s.course, s.semester,
                  m.id AS mark_id, m.percentage, m.grade, m.result_status
           FROM students s
           LEFT JOIN marks m ON m.student_id = s.id
           ORDER BY s.id ASC";
$result = mysqli_query($conn, $query);

require 'includes/header.php';
?>

<section class="card">
    <h2>All Students</h2>
    <div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Roll No</th>
                <th>Enrollment No</th>
                <th>Name</th>
                <th>Course</th>
                <th>Semester</th>
                <th>Percentage</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($result) === 0): ?>
            <tr><td colspan="8" class="text-center">No students found. <a href="add_student.php">Add one</a>.</td></tr>
        <?php else: ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo clean($row['roll_no']); ?></td>
                <td><?php echo clean($row['enrollment_no']); ?></td>
                <td><?php echo clean($row['name']); ?></td>
                <td><?php echo clean($row['course']); ?></td>
                <td><?php echo clean($row['semester']); ?></td>
                <td><?php echo $row['mark_id'] ? number_format($row['percentage'], 2) . '%' : '-'; ?></td>
                <td>
                    <?php if ($row['mark_id']): ?>
                        <span class="badge badge-<?php echo strtolower($row['result_status']); ?>"><?php echo $row['result_status']; ?></span>
                    <?php else: ?>
                        <span class="badge badge-pending">Pending</span>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="enter_marks.php?id=<?php echo (int) $row['id']; ?>"><?php echo $row['mark_id'] ? 'Edit Marks' : 'Enter Marks'; ?></a>
                    <a href="result.php?id=<?php echo (int) $row['id']; ?>">View Result</a>
                    <a href="edit_student.php?id=<?php echo (int) $row['id']; ?>">Edit</a>
                    <a href="delete_student.php?id=<?php echo (int) $row['id']; ?>" class="link-danger"
                       onclick="return confirm('Delete this student and all related marks?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</section>

<?php require 'includes/footer.php'; ?>
