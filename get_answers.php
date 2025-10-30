<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$file = __DIR__ . '/answers.txt';

if (!file_exists($file) || filesize($file) === 0) {
    echo "";
    exit;
}

echo file_get_contents($file);
?>
