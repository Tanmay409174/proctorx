<?php
// This will read the content of the warning_flag.txt file and return it
$file_path = 'warning_flag.txt';

// Check if the file exists and is readable
if (file_exists($file_path)) {
    echo file_get_contents($file_path);  // Read and output the contents of the file
} else {
    echo "0";  // If the file doesn't exist, return 0 (no warning)
}
?>
