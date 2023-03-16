<?php

// curl command for install (for update replace i with u):
// bash <(curl -d 'action=i&secret_token=SkctiyZrHdGuJVvQ8Y1w5ttU7EKN1ySeXWPYVhypGg398cOL' -X POST 172.16.13.33/api/)

// $database_host = '172.16.5.1';
// $database_name = 'testuser2';
// $database_user = 'testuser2';
// $database_user_password = 'Jeppe2006';
require '../database.php';

// get the FQDN var
require_once './settings.php';

$conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function doLastSeen($conn, $secret_token) {
  $timestamp = date("Y-m-d H:i:s");
  $sql = "UPDATE services SET seen_date='$timestamp' WHERE secret_token='$secret_token'";
  $result = $conn->query($sql);
}

function doUpdate() {
    echo 'echo $USER > /tmp/idk';
}

// serve the bash code to install the cronjob in the /etc/cron.d dir
function doInstall($displayname, $secret_token) {
    echo "clear \n";
    echo "echo \n";
    echo "echo Installing cron.d file for Pterodactyl Server Backup Manager \n";
    echo "echo Service: " . $displayname . "\n";

    echo "echo '# /etc/cron.d/backupmanager: crontab entry for the Pterodactyl Server Backup Manager' > /etc/cron.d/backupmanager \n";
    echo "echo 'SHELL=/bin/sh' >> /etc/cron.d/backupmanager \n";
    echo "echo 'PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin' >> /etc/cron.d/backupmanager \n";
    $cmd = 'curl -d "action=u&secret_token=' . $secret_token . '" -X POST ' . $install_url . ' | bash';
    echo "echo '* * * * * root " . $cmd . "' >> /etc/cron.d/backupmanager \n";
}

// check if the POST request is complete
if (!isset($_POST['action']) AND !isset($_POST['secret_token'])) {
    die('no POST data');
}

// quick code to kinda stop user inputted strings from being malicious
$action = htmlspecialchars($_POST['action']);
$secret_token = htmlspecialchars($_POST['secret_token']);

// check the inputted token length, easy way to stop malicios intent.
if (strlen($secret_token) != 48) {
    die('No such token');
}

// check if the token only consists of numbers and letters, if not then it dies.
if (ctype_alnum($secret_token) == false) {
  die('No such token');
}

// check the token with the database, if the token exists, it pulls the row with the node info.
$result = mysqli_query($conn,"SELECT * FROM `services` WHERE secret_token = '$secret_token'");
if ($result->num_rows > 0) {
  // store the info in variables
  while($row = $result->fetch_assoc()) {
    $displayname = $row['displayname'];
    $db_ipaddress = $row['ipaddress'];
    $ip_locked = $row['ip_locked'];
    $client_ipadress = $_SERVER['REMOTE_ADDR'];
  }
} else {
    die('No such token');
}

// check whether any ip is allowed for this token, if not then check if the client ip matches the ip in the db, if not then it dies.
if ($ip_locked == 'true' AND $db_ipaddress != $client_ipadress) {
  die('Unauthorized access (ip)');
}

// the request checks out, and we're ready to either serve the update/install script
if ($action == 'u') {
    // run the update function, duh
    doLastSeen($conn, $secret_token);
    doUpdate();
 } elseif ($action == 'i') {
    // first thing first, we gotta set the service ip in the db
    // check the ip syntax
    if (filter_var($client_ipadress, FILTER_VALIDATE_IP)) {
      // the client ip is a valid ip adress, so we write it to the db
      $sql = "UPDATE services SET ipaddress='$client_ipadress' WHERE secret_token='$secret_token'";
      if ($conn->query($sql) === TRUE) {
        // return the oneliner to install the cronjob
        doLastSeen($conn, $secret_token);
        doInstall($displayname, $secret_token);
      } else {
        echo "Error setting IP: " . $conn->error;
      }
    } else {
      echo("$client_ipadress is not a valid IP address");
    }   
} else {
  // no known access specified
   die('Unknown action');
}

// close the database connection
$conn->close();
?>