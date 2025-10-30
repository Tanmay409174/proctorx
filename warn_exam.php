<?php
if (isset($_POST['warning']) && $_POST['warning'] == '1') {
    // Set the flag for warning, which the frontend can use to show the warning message
    file_put_contents('warning_flag.txt', '1');
    echo 'Warning sent';
}
?>
