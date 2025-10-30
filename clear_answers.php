<?php
$file = __DIR__ . '/answers.txt';
if (file_exists($file)) {
    file_put_contents($file, "");
    echo "✅ All student responses deleted successfully.";
} else {
    echo "⚠️ answers.txt not found.";
}
?>
