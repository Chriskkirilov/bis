<?php
session_start();
include 'config.php';

$student_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

if (!$student_id) {
    die("Student ID not found. Please ensure you are enrolled.");
}

$answers = $_POST['answers'] ?? [];
$total_score = 0;
$max_score = 0;

// Check if answers exist
if (!empty($answers)) {
    foreach ($answers as $quiz_id => $question_answers) {
        $total_questions_query = "SELECT COUNT(*) AS total_questions FROM Questions WHERE quiz_id = ?";
        $total_questions_stmt = $mysqli->prepare($total_questions_query);
        $total_questions_stmt->bind_param("i", $quiz_id);
        $total_questions_stmt->execute();
        $total_questions_result = $total_questions_stmt->get_result();
        $total_questions = $total_questions_result->fetch_assoc()['total_questions'];

        $max_score += $total_questions;

        foreach ($question_answers as $question_id => $selected_option_id) {
            $correct_option_query = "SELECT option_id FROM Options WHERE question_id = ? AND is_correct = 1";
            $stmt = $mysqli->prepare($correct_option_query);
            $stmt->bind_param("i", $question_id);
            $stmt->execute();
            $correct_option_result = $stmt->get_result();

            if ($correct_option_result->num_rows > 0) {
                $correct_option = $correct_option_result->fetch_assoc()['option_id'];

                $is_correct = ($selected_option_id == $correct_option);
                
                $score = $is_correct ? 100 : 0; 
                $total_score += $is_correct ? 1 : 0;

                $existing_answer_query = "SELECT answer_id FROM StudentQuizAnswers WHERE student_id = ? AND quiz_id = ? AND question_id = ?";
                $existing_answer_stmt = $mysqli->prepare($existing_answer_query);
                $existing_answer_stmt->bind_param("iii", $student_id, $quiz_id, $question_id);
                $existing_answer_stmt->execute();
                $existing_answer_result = $existing_answer_stmt->get_result();

                if ($existing_answer_result->num_rows > 0) {
                    $update_query = "UPDATE StudentQuizAnswers SET selected_option_id = ?, score = ? WHERE student_id = ? AND quiz_id = ? AND question_id = ?";
                    $update_stmt = $mysqli->prepare($update_query);
                    $update_stmt->bind_param("iidii", $selected_option_id, $score, $student_id, $quiz_id, $question_id);
                    if (!$update_stmt->execute()) {
                        echo "Error updating record: " . $update_stmt->error;
                    }
                } else {
                    $insert_query = "INSERT INTO StudentQuizAnswers (student_id, quiz_id, question_id, selected_option_id, score) VALUES (?, ?, ?, ?, ?)";
                    $insert_stmt = $mysqli->prepare($insert_query);
                    $insert_stmt->bind_param("iidii", $student_id, $quiz_id, $question_id, $selected_option_id, $score);
                    if (!$insert_stmt->execute()) {
                        echo "Error inserting record: " . $insert_stmt->error;
                    }
                }
            } else {
                echo "No correct option found for question ID: $question_id<br>";
            }
            $stmt->close();
        }
    }
} else {
    echo "No answers submitted.";
}

$percentage_score = $max_score > 0 ? ($total_score / $max_score) * 100 : 0; // Prevent division by zero

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Quiz Submitted</h1>
    <h2>Your Results</h2>
    <p>Your score: <?= round($percentage_score) ?>% / 100%</p>

    <form action="all_quizzes.php" method="get">
        <input type="submit" value="Go to Remaining Quizzes">
        <br>
    </form>
    <form action="index.php" method="get">
        <input type="submit" value="Go to Dashboard">
    </form>
</div>

</body>
</html>
