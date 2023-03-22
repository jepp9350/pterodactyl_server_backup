<?php
// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    // If the user isn't logged in, redirect them to the login page
    header("Location: login.php");
    exit();
}
// Check if the backup server id is set
if (!isset($_GET['backup_server_id'])) {
    echo "Invalid, missing server_backup_id!";
    exit();
}
// Get database credentials
require_once './database.php';
// Connect to the database
$conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Get the backup server id
$backup_server_id = $_GET['backup_server_id'];
// Get the backup server info
$sql = "SELECT * FROM saved_backups WHERE backup_server='$backup_server_id' ORDER BY backup_date DESC LIMIT 25";
$result = $conn->query($sql);
// Prepare list of backups
$backups_items = '';
// add the columns div
$backups_items .= '<div class="columns is-multiline">';
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $backup_path = $row['backup_path'];
        $backup_size = $row['backup_size'];
        $backup_date = $row['reg_date'];
        $backup_type = $row['backup_type'];
        $backup_status = $row['backup_status'];
        // Get the file name from the path (last part of the path)
        

        // User friendly backup name
        switch ($backup_type) {
            case "mysql":
                $backup_type = "MySQL";
                break;
            case "files":
                $backup_type = "Files";
                break;
            default:
                $backup_type = "Unknown";
        }
        // User friendly backup size
        if ($backup_size > 1000000000) {
            $backup_size_friendly = round($backup_size / 1000000000, 2) . ' GB';
        } else {
            if ($backup_size > 1000000) {
                $backup_size_friendly = round($backup_size / 1000000, 2) . ' MB';
            } else {
                if ($backup_size > 1000) {
                    $backup_size_friendly = round($backup_size / 1000, 2) . ' KB';
                } else {
                    $backup_size_friendly = $backup_size . ' B';
                }
            }
        }
        $backup_file_name = $backup_type . ' - ' . time_since($backup_date);
        // Prepare the backup item
        $backups_items .= '<div class="column is-half"><div class="box">
        <div class="field"><strong>'.$backup_type.'</strong> - '.time_since($backup_date).'</div>
        <div class="field"><strong>Backup size:</strong> '.$backup_size_friendly.'</div>
        <div class="field"><strong>Backup status:</strong> '.$backup_status.'</div>
        <div class="field"><strong>Backup date:</strong> '.$backup_date.'</div>
        <button onclick="download_backup('.$row['id'].')" class="button is-link">Download</button>
        </div></div>';


    }
} else {
    echo "0 results";
}
// Close the connection
$conn->close();
// add the end div
$backups_items .= '</div>';
// Return the backups list
echo $backups_items;

function time_since($backup_date) {

        // Time since backup_date (in seconds)
        $time_since_backup = time() - strtotime($backup_date);
        // Time since backup_date (in days)
        $time_since_backup_days = floor($time_since_backup / (60 * 60 * 24));
        // Time since backup_date (in hours)
        $time_since_backup_hours = floor($time_since_backup / (60 * 60));
        // Time since backup_date (in minutes)
        $time_since_backup_minutes = floor($time_since_backup / (60));
        // Time since backup_date (in seconds)
        $time_since_backup_seconds = floor($time_since_backup);
        // If the backup is older than 1 day
        if ($time_since_backup_days > 0) {
            // Set the backup date to the time since backup
            $backup_date = $time_since_backup_days . ' days ago';
        } else {
            // If the backup is older than 1 hour
            if ($time_since_backup_hours > 0) {
                // Set the backup date to the time since backup
                if ($time_since_backup_hours == 1) {
                    $backup_date = $time_since_backup_hours . ' hour ago';
                } else {
                    $backup_date = $time_since_backup_hours . ' hours ago';
                }
            } else {
                // If the backup is older than 1 minute
                if ($time_since_backup_minutes > 0) {
                    // Set the backup date to the time since backup
                    $backup_date = $time_since_backup_minutes . ' minutes ago';
                } else {
                    // Set the backup date to the time since backup
                    $backup_date = $time_since_backup_seconds . ' seconds ago';
                }
            }
        }
    return $backup_date;
    }
?>