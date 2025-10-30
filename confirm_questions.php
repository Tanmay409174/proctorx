<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use absolute paths to avoid confusion
$sourceFile = __DIR__ . '/questions.txt';
$targetFile = __DIR__ . '/confirmed_questions.txt';

// 1️⃣ Check if source file exists
if (!file_exists($sourceFile)) {
    echo "❌ Error: questions.txt not found in " . __DIR__;
    exit;
}

// 2️⃣ Read source file content
$data = trim(file_get_contents($sourceFile));

// 3️⃣ Handle empty file
if ($data === "") {
    echo "❌ No questions found in questions.txt to confirm.";
    exit;
}

// 4️⃣ Overwrite confirmed_questions.txt completely
if (file_put_contents($targetFile, $data) !== false) {
    echo "✅ Questions confirmed successfully for the exam!";
} else {
    echo "❌ Error: Unable to write to confirmed_questions.txt. Check permissions.";
}
?>
