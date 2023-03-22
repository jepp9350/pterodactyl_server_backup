<?php
// Path: cron.php
// Start of the cron.php file
// Update the last seen date of the backup servers
require_once './settings.php';
require_once './functions.php';
// Test if the database is connected
if (check_database_connection("validate","default") != "connected"){
    echo "Database connection failed!";
    exit();
}
// Include the Net_SSH2 library
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
include('Net/SSH2.php');
// Require the database settings
require './database.php';
// Create connection
$conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
// Check connection for errors
if ($conn->connect_error) {
    echo "Failed to connect to database.";
    return 500;
}
// Get the current date
$current_date = date("Y-m-d H:i:s");
// Add the event to the server logs table
log_event("cron","Cron job started.",null,null);
// Get the backup servers
$sql = "SELECT * FROM backup_servers";
$result = $conn->query($sql);
$servers = array();
// Loop through the backup servers and update the last seen date
foreach ($result as $server_array) {
    // Check if the ssh values are set
    if (!isset($server_array["ssh_username"]) || !isset($server_array["ssh_password"]) || !isset($server_array["ssh_port"])) {
        // If the ssh values are not set, skip the server
        //echo "Skipping server ".$server_array["id"]." (".$server_array["displayname"].")... ";
        // log the event to the server logs table
        log_event("cron_error","Cron job skipped server - Missing SSH credentials.".$server_array["id"]." (".$server_array["displayname"].").",$server_array["id"],null);
        continue;
    }
    // Connect to the backup server, to check if it's online
    $ssh = new Net_SSH2($server_array["ipaddress"], $server_array["ssh_port"]);
    if (!$ssh->login($server_array["ssh_username"], $server_array["ssh_password"])) {
        // If the server is offline, skip it
        //echo "Skipping server ".$server_array["id"]." (".$server_array["displayname"].")... ";
        // log the event to the server logs table
        log_event("cron_error","Cron job skipped server ".$server_array["id"]." (".$server_array["displayname"].").",$server_array["id"],null);
        continue;
    }
    // If the server is online, update the last seen date
    //echo "Updating server ".$server_array["id"]." (".$server_array["displayname"].")... ";
    $sql = "UPDATE backup_servers SET seen_date = '".$current_date."' WHERE id = '".$server_array["id"]."'";
    $result_update = $conn->query($sql);
    // log the event to the server logs table
    log_event("cron_success","Cron job updated server ".$server_array["id"]." (".$server_array["displayname"].").",$server_array["id"],null);
}
// Close the database connection
$conn->close();
// log the event to the server logs table
log_event("cron","Cron job finished.",null,null);
function check_for_backup_jobs() {
    // Function to check if a backup should be created
    // Get the database settings
    require './database.php';
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection for errors
    if ($conn->connect_error) {
        echo "Failed to connect to database.";
        return 500;
    }
    // Get the current date
    $current_date = date("Y-m-d H:i:s");
    // Get the backup jobs
    $sql = "SELECT * FROM backup_plans WHERE next_run_date <= '".$current_date."'";
    // Send the query to the database
    $result = $conn->query($sql);
    // Format the result as an array
    $backup_jobs = $result->fetch_assoc();
    // if result array is larger than 0, continue
    if ($result > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            // Get the backup plan
            $backup_plan = $row;
            echo 'Backup plan: '.$backup_plan["displayname"];
            }
    } else {
        // If there's no pending backups, log the event
        log_event("cron","No pending backups.",null,null);
        }
    // Close the database connection
    $conn->close();
}
function backup_database($service_id, $backup_server_id, $backup_plan_id) {
    // Get the database settings
    require './database.php';
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection for errors
    if ($conn->connect_error) {
        echo "Failed to connect to database.";
        return 500;
    }
    // Get the backup plan
    $sql = "SELECT * FROM backup_plans WHERE id = '".$backup_plan_id."'";
    // Send the query to the database
    $result = $conn->query($sql);
    // Format the result as an array
    $backup_plan = $result->fetch_assoc();
    // if result array is larger than 0, continue
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            // Get the backup plan
            $backup_plan = $row;
            echo 'Backup plan: '.$backup_plan["displayname"];
            }
        } else {
            // If the backup plan does not exist, return an error
            echo "Backup plan does not exist.";
            return 500;
        }
    // Get the backup server
    $sql = "SELECT * FROM backup_servers WHERE id = '".$backup_server_id."'";
    // Send the query to the database
    $result = $conn->query($sql);
    // Format the result as an array
    $backup_server = $result->fetch_assoc();
    // if result array is larger than 0, continue
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            // Get the backup server
            $backup_server = $row;
            echo 'Backup server: '.$backup_server["displayname"];
            }
        } else {
            // If the backup server does not exist, return an error
            echo "Backup server does not exist.";
            return 500;
        }
}
?>