<?php
if (isset($_POST['index'], $_POST['question'], $_POST['options'], $_POST['correct'])) {
    $index = (int)$_POST['index'];
    $question = trim($_POST['question']);
    $options = trim($_POST['options']);
    $correct = trim($_POST['correct']);

    $lines = file("questions.txt", FILE_IGNORE_NEW_LINES);
    if (isset($lines[$index])) {
        $lines[$index] = "$question|$options|$correct|null";
        file_put_contents("questions.txt", implode("\n", $lines));
    }
}
?>
