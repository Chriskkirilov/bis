<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $issue_date = $_POST['issue_date'];
    $certificate_url = $_POST['certificate_url'];
    
    $student_check_query = "SELECT user_id FROM Users WHERE user_id = ?";
    $stmt = $mysqli->prepare($student_check_query);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student_check_result = $stmt->get_result();

    if ($student_check_result->num_rows > 0) {
        $sql = "INSERT INTO Certificates (student_id, course_id, issue_date, certificate_url) 
                VALUES (?, ?, ?, ?)";

        $insert_stmt = $mysqli->prepare($sql);
        $insert_stmt->bind_param("iiss", $student_id, $course_id, $issue_date, $certificate_url);

        if ($insert_stmt->execute()) {
            echo "<p class='success-msg'>Certificate issued successfully.</p>";
        } else {
            echo "<p class='error-msg'>Error: " . $insert_stmt->error . "</p>";
        }
    } else {
        echo "<p class='error-msg'>Error: Student ID does not exist.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Certificate</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Issue Certificate</h1>

    <form action="add_certificate.php" method="post">
        <label for="student_id">Student:</label>
        <select id="student_id" name="student_id" required>
            <?php
            $result = $mysqli->query("SELECT user_id, full_name FROM Users WHERE role = 'student'");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['user_id']}'>{$row['full_name']}</option>";
            }
            ?>
        </select>

        <label for="course_id">Course:</label>
        <select id="course_id" name="course_id" required>
            <?php
            $result = $mysqli->query("SELECT course_id, course_title FROM Courses");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['course_id']}'>{$row['course_title']}</option>";
            }
            ?>
        </select>

        <label for="issue_date">Issue Date:</label>
        <input type="date" id="issue_date" name="issue_date" required>

        <label for="certificate_url">Certificate URL:</label>
        <input type="text" id="certificate_url" name="certificate_url" placeholder="Enter URL or leave empty">

        <input type="submit" value="Issue Certificate">
    </form>

    <a href="index.php">Go back to Dashboard</a>
</div>

</body>
</html>
