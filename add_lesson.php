<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lesson_title = $_POST['lesson_title'];
    $course_id = $_POST['course_id'];
    $lesson_content = $_POST['lesson_content'];
    $video_url = $_POST['video_url'];
    
    $sql = "INSERT INTO Lessons (lesson_title, course_id, lesson_content, video_url) 
            VALUES ('$lesson_title', '$course_id', '$lesson_content', '$video_url')";

    if ($mysqli->query($sql)) {
        echo "<p class='success-msg'>New lesson added successfully.</p>";
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
    <title>Add New Lesson</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Add New Lesson</h1>

    <form action="add_lesson.php" method="post">
        <label for="lesson_title">Lesson Title:</label>
        <input type="text" id="lesson_title" name="lesson_title" required>

        <label for="course_id">Course:</label>
        <select id="course_id" name="course_id" required>
            <?php
            $result = $mysqli->query("SELECT course_id, course_title FROM Courses");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['course_id']}'>{$row['course_title']}</option>";
            }
            ?>
        </select>

        <label for="lesson_content">Lesson Content:</label>
        <textarea id="lesson_content" name="lesson_content" required></textarea>

        <label for="video_url">Video URL:</label>
        <input type="text" id="video_url" name="video_url">

        <input type="submit" value="Add Lesson">
    </form>

    <a href="index.php">Go back to Dashboard</a>
</div>

</body>
</html>
