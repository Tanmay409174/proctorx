<?php
$file = "questions.txt";

if (file_exists($file)) {
    file_put_contents($file, ""); // empties questions.txt
    echo "Questions cleared successfully.";
} else {
    echo "questions.txt not found.";
}
?>
