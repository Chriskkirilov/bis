<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Learning Platform</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Online Learning Platform Dashboard</h1>

    <div class="button">
        <a href="add_user.php" class="dashboard-button">Add New User</a>
        <a href="add_course.php" class="dashboard-button">Add New Course</a>
        <a href="add_lesson.php" class="dashboard-button">Add New Lesson</a>
        <a href="add_quiz.php" class="dashboard-button">Add New Quiz</a>
        <a href="add_question.php" class="dashboard-button">Add New Question</a>
        <a href="add_certificate.php" class="dashboard-button">Add Certificate</a>
        <a href="enroll_students.php" class="dashboard-button">Enroll a Student</a>
    </div>

    <h2>Users Table</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
        <?php
        $result = $mysqli->query("SELECT * FROM Users");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['user_id']}</td>
                    <td>{$row['full_name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['role']}</td>
                  </tr>";
        }
        ?>
    </table>

    <h2>Courses Table</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Instructor ID</th>
            <th>Price ($)</th>
        </tr>
        <?php
        $result = $mysqli->query("SELECT * FROM Courses");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['course_id']}</td>
                    <td>{$row['course_title']}</td>
                    <td>{$row['instructor_id']}</td>
                    <td>{$row['price']}</td>
                  </tr>";
        }
        ?>
    </table>

    <h2>Lessons Table</h2>
    <table>
        <tr>
            <th>Lesson ID</th>
            <th>Lesson Title</th>
            <th>Course Title</th>
            <th>Lesson Content</th>
        </tr>
        <?php
        $lessons_query = "
            SELECT L.lesson_id, L.lesson_title, C.course_title, L.video_url
            FROM Lessons L
            JOIN Courses C ON L.course_id = C.course_id
            ORDER BY L.lesson_title
        ";

        $lessons_result = $mysqli->query($lessons_query);

        if ($lessons_result->num_rows > 0) {
            while ($row = $lessons_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['lesson_id']) . "</td>
                        <td>" . htmlspecialchars($row['lesson_title']) . "</td>
                        <td>" . htmlspecialchars($row['course_title']) . "</td>
                <td><a href='" . htmlspecialchars($row['video_url']) . "'>" . htmlspecialchars($row['lesson_title']) . " recording</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No lessons found.</td></tr>";
        }
        ?>
    </table>

    <h2>Issued Certificates</h2>
    <table>
        <tr>
            <th>Student Name</th>
            <th>Course Title</th>
            <th>Issue Date</th>
            <th>Certificate URL</th>
        </tr>
        <?php
        $certificates_query = "
            SELECT U.full_name AS student_name, 
                   C.course_title, 
                   Cert.issue_date, 
                   Cert.certificate_url
            FROM Certificates Cert
            JOIN Users U ON Cert.student_id = U.user_id
            JOIN Courses C ON Cert.course_id = C.course_id
            ORDER BY U.full_name, Cert.issue_date DESC
        ";

        $certificates_result = $mysqli->query($certificates_query);

        if ($certificates_result->num_rows > 0) {
            while ($row = $certificates_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['student_name']) . "</td>
                        <td>" . htmlspecialchars($row['course_title']) . "</td>
                        <td>" . htmlspecialchars($row['issue_date']) . "</td>
                        <td><a href='" . htmlspecialchars($row['certificate_url']) . "'>View Certificate</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No certificates issued.</td></tr>";
        }
        ?>
    </table>

    <h2>Quiz Results of Students</h2>
    <?php
    $students_query = "SELECT DISTINCT U.user_id, U.full_name FROM Users U WHERE U.role = 'student'";
    $students_result = $mysqli->query($students_query);

    if ($students_result->num_rows > 0) {
        while ($student = $students_result->fetch_assoc()) {
            echo "<h3>" . htmlspecialchars($student['full_name']) . "</h3>"; // Display student name
            
            $quiz_results_query = "
                SELECT Q.quiz_title, 
                       SUM(A.score) AS total_score, 
                       COUNT(A.question_id) AS total_questions, 
                       (SUM(A.score) / COUNT(A.question_id)) AS percentage_score
                FROM StudentQuizAnswers A
                JOIN Questions Q2 ON A.question_id = Q2.question_id
                JOIN Quizzes Q ON Q2.quiz_id = Q.quiz_id
                WHERE A.student_id = ? 
                GROUP BY Q.quiz_id
            ";

            $quiz_stmt = $mysqli->prepare($quiz_results_query);
            $quiz_stmt->bind_param("i", $student['user_id']);
            $quiz_stmt->execute();
            $quiz_results_result = $quiz_stmt->get_result();

            echo "<table>
                    <tr>
                        <th>Quiz Title</th>
                        <th>Score (%)</th>
                        <th>Total Questions</th>
                    </tr>";

            if ($quiz_results_result->num_rows > 0) {
                while ($result = $quiz_results_result->fetch_assoc()) {
                    $percentage_score = round($result['percentage_score'], 2);
                    echo "<tr>
                            <td>" . htmlspecialchars($result['quiz_title']) . "</td>
                            <td>" . htmlspecialchars($percentage_score) . "%</td>
                            <td>" . htmlspecialchars($result['total_questions']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No quiz results found for this student.</td></tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p>No students found.</p>";
    }
    ?>
</div>

</body>
</html>
