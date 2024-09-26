<?php
include 'config.php';

$quiz_id = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_question'])) {
    $quiz_id = $_POST['quiz_id'];
    $question_text = $_POST['question_text'];

    $sql = "INSERT INTO Questions (quiz_id, question_text) 
            VALUES ('$quiz_id', '$question_text')";
    
    if ($mysqli->query($sql)) {
        $question_id = $mysqli->insert_id;
        $message = "Question added successfully. Now add options.";
    } else {
        $error = "Error: " . $mysqli->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_option'])) {
    $question_id = $_POST['question_id'];
    $option_text = $_POST['option_text'];
    $is_correct = isset($_POST['is_correct']) ? 1 : 0;

    $sql = "INSERT INTO Options (question_id, option_text, is_correct) 
            VALUES ('$question_id', '$option_text', '$is_correct')";
    
    if ($mysqli->query($sql)) {
        $option_message = "Option added successfully.";
    } else {
        $option_error = "Error: " . $mysqli->error;
    }
}

$quizzes = [];
$result = $mysqli->query("SELECT quiz_id, quiz_title FROM Quizzes");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Add Question</h1>

    <form action="add_question.php" method="post">
        <label for="quiz_id">Select Quiz:</label>
        <select id="quiz_id" name="quiz_id" required>
            <option value="">Select Quiz</option>
            <?php foreach ($quizzes as $quiz): ?>
                <option value="<?= $quiz['quiz_id'] ?>"><?= htmlspecialchars($quiz['quiz_title']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="question_text">Question Text:</label>
        <textarea id="question_text" name="question_text" required></textarea>

        <input type="submit" name="add_question" value="Add Question">
    </form>

    <?php if (isset($message)): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p><?= $error ?></p>
    <?php endif; ?>

    <?php if (isset($question_id)): ?>
        <h2>Add Options for Question ID: <?= $question_id ?></h2>
        <form action="add_question.php" method="post">
            <input type="hidden" name="question_id" value="<?= $question_id ?>">

            <label for="option_text">Option Text:</label>
            <input type="text" id="option_text" name="option_text" required>

            <label for="is_correct">Is Correct:</label>
            <input type="checkbox" id="is_correct" name="is_correct">

            <input type="submit" name="add_option" value="Add Option">
            
            <a href="index.php">Go back to Dashboard</a>

        </form>

        <?php if (isset($option_message)): ?>
            <p><?= $option_message ?></p>
        <?php endif; ?>

        <?php if (isset($option_error)): ?>
            <p><?= $option_error ?></p>
        <?php endif; ?>
    <?php endif; ?>

</div>

</body>
</html>
