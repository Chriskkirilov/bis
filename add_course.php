<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_title = $_POST['course_title'];
    $instructor_id = $_POST['instructor_id'];
    $course_description = $_POST['course_description'];
    $course_duration = $_POST['course_duration'];
    $course_language = $_POST['course_language'];
    $creation_date = $_POST['creation_date'];
    $price = $_POST['price'];

    $sql = "INSERT INTO Courses (course_title, instructor_id, course_description, course_language, creation_date, course_duration, price) 
            VALUES ('$course_title', '$instructor_id', '$course_description', '$course_language', '$creation_date', '$course_duration', '$price')";

    if ($mysqli->query($sql)) {
        echo "New course added successfully.";
    } else {
        echo "Error: " . $mysqli->error;
    }
}

$instructors = [];
$result = $mysqli->query("SELECT user_id, full_name FROM users WHERE role = 'instructor'");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Add New Course</h1>
    <form action="add_course.php" method="post">
        <label for="course_title">Course Title:</label>
        <input type="text" id="course_title" name="course_title" required>

        <label for="instructor_id">Instructor:</label>
        <select id="instructor_id" name="instructor_id" required>
            <option value="">Select Instructor</option>
            <?php foreach ($instructors as $instructor): ?>
                <option value="<?= $instructor['user_id'] ?>"><?= htmlspecialchars($instructor['full_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="course_description">Course Description:</label>
        <textarea id="course_description" name="course_description" required></textarea>

        <label for="course_duration">Course Duration:</label>
        <select id="course_hour" name="course_duration" required>
            <option style="display:none;" selected>e.g. 2 hours</option>
            <?php for ($h = 0; $h < 24; $h++): ?>
                <option value="<?= $h ?>"><?= sprintf('%02d', $h) ?></option>
            <?php endfor; ?>
        </select>

        <label for="course_language">Course Language:</label>
        <select id="course_language" name="course_language" required>
            <option value="">Select Language</option>
            <option value="English">English</option>
            <option value="Spanish">Spanish</option>
            <option value="French">French</option>
            <option value="German">German</option>
            <option value="Chinese">Chinese</option>
            <option value="Other">Other</option>
        </select>

        <label for="creation_date">Creation Date:</label>
        <input type="date" id="creation_date" name="creation_date" required>

        <label for="price">Price (in $):</label>
        <input type="number" id="price" name="price" required>

        <input type="submit" value="Add Course">
    </form>

    <a href="index.php">Go back to Dashboard</a>
</div>

</body>
</html>
