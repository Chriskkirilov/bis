<?php
session_start();
include 'config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: enroll_students.php");
    exit;
}

$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : null;

if (!$quiz_id) {
    die("No quiz selected.");
}

$quiz_query = "SELECT * FROM Quizzes WHERE quiz_id = ?";
$stmt = $mysqli->prepare($quiz_query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz_result = $stmt->get_result();

if ($quiz_result->num_rows === 0) {
    die("Quiz not found.");
}

$quiz = $quiz_result->fetch_assoc();
$quiz_title = htmlspecialchars($quiz['quiz_title']);

$questions_query = "SELECT * FROM Questions WHERE quiz_id = ?";
$questions_stmt = $mysqli->prepare($questions_query);
$questions_stmt->bind_param("i", $quiz_id);
$questions_stmt->execute();
$questions_result = $questions_stmt->get_result();

if ($questions_result->num_rows === 0) {
    die("No questions found for this quiz.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $quiz_title ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1><?= $quiz_title ?></h1>
    <form action="submit_quiz.php" method="POST">
        <?php while ($question = $questions_result->fetch_assoc()): ?>
            <div class="question">
                <h3><?= htmlspecialchars($question['question_text']) ?></h3>
                <?php
                $options_query = "SELECT * FROM Options WHERE question_id = ?";
                $options_stmt = $mysqli->prepare($options_query);
                $options_stmt->bind_param("i", $question['question_id']);
                $options_stmt->execute();
                $options_result = $options_stmt->get_result();
                ?>
                <?php while ($option = $options_result->fetch_assoc()): ?>
                    <label>
                        <input type="radio" name="answers[<?= $quiz_id ?>][<?= $question['question_id'] ?>]" value="<?= $option['option_id'] ?>" required>
                        <?= htmlspecialchars($option['option_text']) ?>
                    </label><br>
                <?php endwhile; ?>
            </div>
        <?php endwhile; ?>
        <input type="submit" value="Submit Quiz">
    </form>
</div>

</body>
</html>
