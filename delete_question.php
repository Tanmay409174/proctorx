<?php
$index = $_POST['index'];
$lines = file("questions.txt", FILE_IGNORE_NEW_LINES);
unset($lines[$index]);
file_put_contents("questions.txt", implode("\n", $lines));
?>
