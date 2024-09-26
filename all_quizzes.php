<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: enroll_students.php");
    exit;
}

$student_id = $_SESSION['user_id'];

$courses_query = "
    SELECT course_id 
    FROM Enrollments 
    WHERE student_id = ?";
$courses_stmt = $mysqli->prepare($courses_query);
$courses_stmt->bind_param("i", $student_id);
$courses_stmt->execute();
$courses_result = $courses_stmt->get_result();

$course_ids = [];
while ($row = $courses_result->fetch_assoc()) {
    $course_ids[] = $row['course_id'];
}

$quizzes_query = "
    SELECT q.quiz_id, q.quiz_title 
    FROM Quizzes q 
    WHERE q.course_id IN (" . implode(',', $course_ids) . ")";
$quizzes_stmt = $mysqli->prepare($quizzes_query);
$quizzes_stmt->execute();
$quizzes_result = $quizzes_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Quizzes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Available Quizzes</h1>

    <ul>
        <?php if ($quizzes_result->num_rows > 0): ?>
            <?php while ($quiz = $quizzes_result->fetch_assoc()): ?>
                <li>
                    <a href="quiz.php?quiz_id=<?= $quiz['quiz_id'] ?>">
                        <?= htmlspecialchars($quiz['quiz_title']) ?>
                    </a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No quizzes available for your enrolled courses.</li>
        <?php endif; ?>
    </ul>

</div>

</body>
</html>
