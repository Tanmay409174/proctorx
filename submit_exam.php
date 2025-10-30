<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$questionsFile = __DIR__ . '/confirmed_questions.txt';
$answersFile   = __DIR__ . '/answers.txt';
$thankyouPage  = 'thankyou.html';

// Retrieve student name (from hidden field or session/localstorage)
$studentName = trim($_POST['studentName'] ?? 'Unknown Student');

if (!file_exists($questionsFile) || filesize($questionsFile) === 0) {
    die("❌ No confirmed questions found. Please contact admin.");
}

$lines = file($questionsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$submitted = $_POST;
$totalQuestions = count($lines);
$objectiveScore = 0;
$objectiveTotal = 0;

$output = "---- Submission by $studentName at " . date("Y-m-d H:i:s") . " ----\n";

foreach ($lines as $i => $line) {
    $parts = explode('|', $line);
    $question = trim($parts[0] ?? '');
    $options = isset($parts[1]) ? explode(',', $parts[1]) : [];
    $correct = trim($parts[2] ?? '');
    $type = trim($parts[3] ?? 'mcq');
    $qKey = 'q' . ($i + 1);
    $answer = trim($submitted[$qKey] ?? 'Not Answered');

    $output .= "Q" . ($i + 1) . ": $question\n";
    $output .= "Your Answer: $answer\n";

    if ($type === 'mcq') {
        $output .= "Correct Answer: $correct\n";
        $objectiveTotal++;
        if (strtoupper($answer) === strtoupper($correct)) {
            $objectiveScore++;
        }
    } else {
        $output .= "Status: Pending Evaluation\n";
    }

    $output .= "\n";
}

$output .= "Objective Score: $objectiveScore / $objectiveTotal\n";
$output .= "Subjective Questions: " . ($totalQuestions - $objectiveTotal) . " pending manual evaluation.\n\n";

file_put_contents($answersFile, $output, FILE_APPEND);

// Redirect to thank-you page
header("Location: $thankyouPage?score=$objectiveScore&total=$objectiveTotal");
exit;
?>
