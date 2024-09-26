<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quiz_title = $_POST['quiz_title'];
    $course_id = $_POST['course_id'];
    $lesson_id = $_POST['lesson_id'];
    
    $sql = "INSERT INTO Quizzes (quiz_title, course_id, lesson_id) 
            VALUES ('$quiz_title', '$course_id', '$lesson_id')";

    if ($mysqli->query($sql)) {
        echo "<p class='success-msg'>New quiz added successfully.</p>";
    } else {
        echo "<p class='error-msg'>Error: " . $mysqli->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Quiz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Add New Quiz</h1>

    <form action="add_quiz.php" method="post">
        <label for="quiz_title">Quiz Title:</label>
        <input type="text" id="quiz_title" name="quiz_title" required>

        <label for="course_id">Course:</label>
        <select id="course_id" name="course_id" required>
            <?php
            $result = $mysqli->query("SELECT course_id, course_title FROM Courses");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['course_id']}'>{$row['course_title']}</option>";
            }
            ?>
        </select>

        <label for="lesson_id">Lesson:</label>
        <select id="lesson_id" name="lesson_id" required>
            <?php
            $result = $mysqli->query("SELECT lesson_id, lesson_title FROM Lessons");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['lesson_id']}'>{$row['lesson_title']}</option>";
            }
            ?>
        </select>

        <input type="submit" value="Add Quiz">
    </form>

    <a href="index.php">Go back to Dashboard</a>
</div>

</body>
</html>
