<?php
include 'config.php';

session_start();

$courses = [];
$result = $mysqli->query("SELECT course_id, course_title FROM Courses");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

$students = [];
$result = $mysqli->query("SELECT user_id, full_name FROM Users WHERE role = 'student'");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

$selected_courses = [];

$message = '';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'] ?? null;
    $selected_courses = $_POST['course_ids'] ?? [];

    if ($user_id && !empty($selected_courses)) {
        $student_check = $mysqli->query("SELECT user_id FROM Users WHERE user_id = '$user_id' AND role = 'student'");

        if ($student_check->num_rows > 0) {
            foreach ($selected_courses as $course_id) {
                $enrollment_check = $mysqli->query("SELECT enrollment_id FROM Enrollments WHERE student_id = '$user_id' AND course_id = '$course_id'");

                if ($enrollment_check->num_rows == 0) {
                    $enrollment_date = date('Y-m-d H:i:s');
                    $sql = "INSERT INTO Enrollments (student_id, course_id, enrollment_date, progress_percentage) 
                            VALUES ('$user_id', '$course_id', '$enrollment_date', 0)";

                    if ($mysqli->query($sql)) {
                        $enrollment_id = $mysqli->insert_id;
                        
                        $sql = "INSERT INTO EnrollmentStatus (enrollment_id, status_name) 
                                VALUES ('$enrollment_id', 'in progress')";
                        
                        if (!$mysqli->query($sql)) {
                            $error = "Error setting enrollment status for course $course_id: " . $mysqli->error;
                        }
                    } else {
                        $error = "Error enrolling student in course $course_id: " . $mysqli->error;
                    }
                }
            }

            $_SESSION['user_id'] = $user_id;
            $_SESSION['selected_courses'] = $selected_courses;

            header("Location: all_quizzes.php");
            exit;
        } else {
            $error = "Invalid user ID. Please select a valid student.";
        }
    } else {
        $error = "Please select a student and at least one course.";
    }
}

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $enrollment_result = $mysqli->query("SELECT course_id FROM Enrollments WHERE student_id = '$user_id'");
    while ($row = $enrollment_result->fetch_assoc()) {
        $selected_courses[] = $row['course_id'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Students</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function checkAllToggled() {
            let checkboxes = document.querySelectorAll('input[type="checkbox"]');
            let enrollButton = document.getElementById('enrollButton');

            enrollButton.disabled = !Array.from(checkboxes).every(checkbox => checkbox.checked);
        }

        window.onload = function() {
            checkAllToggled();
        };
    </script>
</head>
<body>

<div class="container">
    <h1>Enroll Students in Courses</h1>

    <form action="enroll_students.php" method="post">
        <label for="user_id">Select Student:</label>
        <select id="user_id" name="user_id" required onchange="this.form.submit();">
            <option value="">Select Student</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= $student['user_id'] ?>" <?= isset($user_id) && $user_id == $student['user_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($student['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <h2>Select Courses:</h2>
        <div>
            <?php foreach ($courses as $course): ?>
                <label>
                    <input type="checkbox" name="course_ids[]" value="<?= $course['course_id'] ?>" 
                        <?= in_array($course['course_id'], $selected_courses) ? 'checked' : '' ?> 
                        onchange="checkAllToggled()"> 
                    <?= htmlspecialchars($course['course_title']) ?>
                </label><br>
            <?php endforeach; ?>
        </div>

        <input type="submit" id="enrollButton" value="Enroll Student" disabled>
    </form>

    <?php if ($message): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p><?= $error ?></p>
    <?php endif; ?>
</div>

</body>
</html>
