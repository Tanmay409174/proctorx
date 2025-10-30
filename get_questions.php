<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$file = __DIR__ . '/questions.txt';

if (!file_exists($file) || filesize($file) === 0) {
    echo json_encode([]);
    exit;
}

$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$questions = [];

foreach ($lines as $line) {
    $parts = explode('|', $line);

    $questionText = trim($parts[0] ?? '');
    $options = isset($parts[1]) && !empty($parts[1]) ? explode(',', trim($parts[1])) : [];
    $correct = trim($parts[2] ?? '');
    $type = trim($parts[3] ?? 'subjective'); // ✅ now properly reading the 4th field

    // clean array
    $questions[] = [
        "question" => $questionText,
        "options" => $options,
        "correct" => $correct,
        "type" => $type
    ];
}

header('Content-Type: application/json');
echo json_encode($questions);
?>
