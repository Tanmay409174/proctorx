<?php
// delete_all_questions.php
$questionsFile = __DIR__ . '/questions.txt';

// Check if the file exists
if (file_exists($questionsFile)) {
    // Delete all content inside the questions file
    file_put_contents($questionsFile, "");
    echo "✅ All questions have been deleted successfully.";
} else {
    echo "❌ Error: Questions file does not exist.";
}
?>
