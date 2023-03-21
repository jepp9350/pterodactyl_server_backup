<?php
// Validate the session
if (!isset($_SESSION['user'])) {
    // If the request has no api key, and the user isn't logged in.
    $return = '<div class="box">Access denied, you are logged out.</div>';
    exit($return);
}
// Check for server issues
// If the cron job is not running, display a warning.
// Check from database if the cron job is running.
require './database.php';
// Create connection
$conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
// Check connection for errors
if ($conn->connect_error) {
    $return = '<div class="box">Failed to connect to database.</div>';
    echo $return;
} else {
    $sql = "SELECT * FROM server_logs WHERE event = 'cron' and description = 'Cron job started.' ORDER BY id DESC LIMIT 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $latest_cron_time = $row['reg_date'];
            // Check if it's been more than 5 minutes since the cron job was last run.
            $current_time = time();
            $cron_time = strtotime($latest_cron_time);
            $time_difference = $current_time - $cron_time;
            if ($time_difference > 300) {
                $cron_running = false;
            } else {
                $cron_running = true;
            }
        }
    } else {
        $cron_running = false;
    }
}
if (!$cron_running) {
    // if the cron job is not running, display a warning.
    // if the cron job has run before, display a warning that it hasn't run in the last 5 minutes.
    if (isset($time_difference)) {
        $return = '<div class="mt-4 box"><div class="notification is-warning"><strong>The cron job is not running!</strong><br>The cron job is not running. Last run: '.seconds_to_time($time_difference).'</div></div>';
    } else {
        $return = '<div class="mt-4 box"><div class="notification is-danger"><strong>The cron job is not running!</strong><br>The cron job is not running, please contact your administrator.</div></div>';
    }
} else if ($cron_running && $time_difference > 300) {
    // If the cron job has not run in the last 5 minutes, display a warning.
        $return = $return . '<div class="mt-4 box"><div class="notification is-warning"><strong>The cron job has not run in the last 5 minutes!</strong><br>The cron job has not run in the last 5 minutes, please contact your administrator.</div></div>';
} else {
    // If the cron job is running, display a success message.
    $return = '<div class="mt-4 box"><div class="notification is-success"><strong>The cron job is running.</strong><br>The cron job is running. Last run: '.seconds_to_time($time_difference).'</div></div>';
}
echo $return;
function seconds_to_time($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    $Readable_answer = $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
    // Remove the days if there are none.
    if (strpos($Readable_answer, '0 days') !== false) {
        $Readable_answer = str_replace('0 days, ', '', $Readable_answer);
    }
    // Remove the hours if there are none.
    if (strpos($Readable_answer, '0 hours') !== false) {
        $Readable_answer = str_replace('0 hours, ', '', $Readable_answer);
    }
    // Remove the minutes if there are none.
    if (strpos($Readable_answer, '0 minutes') !== false) {
        $Readable_answer = str_replace('0 minutes and ', '', $Readable_answer);
    }
    // Remove the seconds if there are none.
    if (strpos($Readable_answer, '0 seconds') !== false) {
        $Readable_answer = str_replace('0 seconds', '', $Readable_answer);
    }
    return $Readable_answer . ' ago';
}
?>