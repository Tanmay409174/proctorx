<?php
// Path to your terminatedflag.txt file
$file_path = 'terminated_flag.txt';

// Check if the file exists before writing
if (file_exists($file_path)) {
    // Reset the terminated flag to 0 after termination
    file_put_contents($file_path, '0');  // Set the flag to 0
    echo "Terminated flag reset to 0.";   // Debugging message
} else {
    echo "terminated_flag.txt not found!";  // Debugging message if file doesn't exist
}
?>
