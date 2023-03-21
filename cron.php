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
    // Connect to the backup server, to check if it's online
    $ssh = new Net_SSH2($server_array["ipaddress"], $server_array["ssh_port"]);
    if (!$ssh->login($server_array["ssh_username"], $server_array["ssh_password"])) {
        // If the server is offline, skip it
        echo "Skipping server ".$server_array["id"]." (".$server_array["displayname"].")... ";
        // log the event to the server logs table
        log_event("cron_error","Cron job skipped server ".$server_array["id"]." (".$server_array["displayname"].").",$server_array["id"],null);
        continue;
    }
    // If the server is online, update the last seen date
    echo "Updating server ".$server_array["id"]." (".$server_array["displayname"].")... ";
    $sql = "UPDATE backup_servers SET seen_date = '".$current_date."' WHERE id = '".$server_array["id"]."'";
    $result_update = $conn->query($sql);
    // log the event to the server logs table
    log_event("cron_success","Cron job updated server ".$server_array["id"]." (".$server_array["displayname"].").",$server_array["id"],null);
}
// Close the database connection
$conn->close();
// log the event to the server logs table
log_event("cron","Cron job finished.",null,null);
?>