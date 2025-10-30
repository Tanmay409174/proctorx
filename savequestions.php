<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$question = trim($_POST['question'] ?? '');
$type = isset($_POST['question_type']) ? trim($_POST['question_type']) : '';

$file = __DIR__ . '/questions.txt';

if ($question === '' || $type === '') {
    echo "❌ Please provide both question and question type.";
    exit;
}

if ($type === 'subjective') {
    $line = "$question|||subjective\n";
} elseif ($type === 'mcq') {
    $a = trim($_POST['option_a'] ?? '');
    $b = trim($_POST['option_b'] ?? '');
    $c = trim($_POST['option_c'] ?? '');
    $d = trim($_POST['option_d'] ?? '');
    $correct = trim($_POST['correct_answer'] ?? '');

    if ($a === '' || $b === '' || $c === '' || $d === '' || $correct === '') {
        echo "❌ Please fill all MCQ fields before saving.";
        exit;
    }

    $line = "$question|$a,$b,$c,$d|$correct|mcq\n";
} else {
    echo "❌ Invalid question type.";
    exit;
}

if (file_put_contents($file, $line, FILE_APPEND) !== false) {
    echo "✅ Question saved successfully!";
} else {
    echo "❌ Error saving question.";
}
?>
