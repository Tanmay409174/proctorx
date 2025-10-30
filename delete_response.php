<?php
$file = __DIR__ . '/answers.txt';

if (!file_exists($file)) {
    echo "⚠️ No responses file found.";
    exit;
}

$index = isset($_POST['index']) ? intval($_POST['index']) : -1;
if ($index < 0) {
    echo "❌ Invalid index.";
    exit;
}

$content = file_get_contents($file);
$submissions = preg_split("/---- Submission by /", $content, -1, PREG_SPLIT_NO_EMPTY);

if (!isset($submissions[$index])) {
    echo "⚠️ Submission not found.";
    exit;
}

unset($submissions[$index]);
$newContent = "";
foreach ($submissions as $s) {
    $newContent .= "---- Submission by " . trim($s) . "\n\n";
}

file_put_contents($file, $newContent);
echo "✅ Submission deleted successfully.";
?>
