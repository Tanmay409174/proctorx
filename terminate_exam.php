<?php
$student = $_POST['studentName'] ? $_POST['studentName'] : 'Unknown';
$logfile = __DIR__ . '/termination_log.txt';  // Ensure proper concatenation
$flagfile = __DIR__ . '/terminated_flag.txt';  // Ensure proper concatenation
$now = date("Y-m-d H:i:s");

// Log the termination action
file_put_contents($logfile, "Terminated by camera monitor - Student: $student - Time: $now\n", FILE_APPEND);

// Set the terminated flag (1 for termination)
file_put_contents($flagfile, "1");

http_response_code(200);
echo "OK - Termination logged successfully";
?>
