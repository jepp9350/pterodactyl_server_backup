<?php

// curl command for install (for update replace i with u):
// bash <(curl -d 'action=i&secret_token=SkctiyZrHdGuJVvQ8Y1w5ttU7EKN1ySeXWPYVhypGg398cOL' -X POST 172.16.13.33)


require_once './database.php';

// get the FQDN var
require_once './settings.php';

$conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function doUpdateDiskInfo($conn, $secret_token, $diskinfo) {
  $diskinfo = json_encode($diskinfo);
  $sql = "UPDATE services SET diskinfo='$diskinfo' WHERE secret_token='$secret_token'";
  $conn->query($sql);
}

function doLastSeen($conn, $secret_token) {
  $timestamp = date("Y-m-d H:i:s");
  $sql = "UPDATE services SET seen_date='$timestamp' WHERE secret_token='$secret_token'";
  $conn->query($sql);
}

function doUpdate($updateactions, $conn, $secret_token) {
  echo json_encode($updateactions[0]);
  
  // remove the now finished/running task from the list of actions, so we can remove only that action from the db
  array_shift($updateactions); 
  $updateactions = json_encode($updateactions);

  $sql = "UPDATE services SET updateactions='$updateactions' WHERE secret_token='$secret_token'";

  if (mysqli_query($conn, $sql)) {
    // success
  } else {
    echo "Error updating record: " . mysqli_error($conn);
  }

}

// serve the bash code to install the cronjob in the /etc/cron.d dir
function doInstall($displayname, $secret_token, $settings_install_url) {
    echo "clear \n";
    echo "echo \n";
    echo "echo Downloading python file \n";
    echo "mkdir /etc/pterodactyl-backup-service \n";
    echo "mkdir /etc/pterodactyl-backup-service/tmp \n";
    echo "wget " . $settings_install_url . "/files/server_refresh.py -O /etc/pterodactyl-backup-service/server_refresh.py \n";
    echo "echo " . $secret_token . " > /etc/pterodactyl-backup-service/secret_token \n";
    echo "echo " . $settings_install_url . " > /etc/pterodactyl-backup-service/install_url \n";
    echo "clear \n";
    echo "echo \n";
    echo "echo Installing cron.d file for Pterodactyl Server Backup Manager \n";
    echo "echo Service: " . $displayname . "\n";
    echo "echo '# /etc/cron.d/backupmanager: crontab entry for the Pterodactyl Server Backup Manager' > /etc/cron.d/backupmanager \n";
    $cmd = 'cd /etc/pterodactyl-backup-service && $(which python3) server_refresh.py > log.txt';
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

// check the token with the database, if the token exists, it pulls the row with the node/service info.
$result = mysqli_query($conn,"SELECT * FROM `services` WHERE secret_token = '$secret_token'");
if ($result->num_rows > 0) {
  // store the info in variables
  while($row = $result->fetch_assoc()) {
    $displayname = $row['displayname'];
    $db_ipaddress = $row['ipaddress'];
    $ip_locked = $row['ip_locked'];
    $client_ipadress = $_SERVER['REMOTE_ADDR'];

    if (isset($row["updateactions"]) && !empty($row["updateactions"])) {
      $updateactions = $row["updateactions"];
      if ($updateactions == '[]' OR $updateactions == '') {
        $updateactions = 'none';
      } else {
        $updateactions = json_decode($updateactions);
      }
    }
  }
} else {
    die('No such token');
}

// check whether any ip is allowed for this token, if not then check if the client ip matches the ip in the db, if not then it dies.
if ($ip_locked == 'true' AND $db_ipaddress != $client_ipadress) {
  die('Unauthorized access (ip)');
}

// the request checks out, and we're ready to either serve the update/install script
if ($action == 'fetchAction') {
    // since it's the update action, we know that diskinfo is POSTed
     $diskinfo = json_decode($_POST['diskinfo'], true);
     doUpdateDiskInfo($conn, $secret_token, $diskinfo);

    // run the timestamp last seen function, duh
    doLastSeen($conn, $secret_token);
    // check if there are any actions, if not then it does nothing
    if ($updateactions == 'none') {
      die('no action');
    }
    doUpdate($updateactions, $conn, $secret_token);

 } elseif ($action == 'i') {
    // first thing first, we gotta set the service ip in the db
    // check the ip syntax
    if (filter_var($client_ipadress, FILTER_VALIDATE_IP)) {
      // the client ip is a valid ip adress, so we write it to the db
      $sql = "UPDATE services SET ipaddress='$client_ipadress' WHERE secret_token='$secret_token'";
      if ($conn->query($sql) === TRUE) {
        // return the oneliner to install the cronjob
        doLastSeen($conn, $secret_token);
        doInstall($displayname, $secret_token, $settings_install_url);
      } else {
        echo "Error setting IP: " . $conn->error;
      }
    } else {
      echo("$client_ipadress is not a valid IP address");
    }  

} elseif ($action == 'uploadBackup') {
  $backupInfo = json_decode($_POST['backup_info'], true);
  
  $backup_plan_id = htmlspecialchars($backupInfo[0]);
  $service_id = htmlspecialchars($backupInfo[1]);
  $backup_server_id = htmlspecialchars($backupInfo[2]);
  $backup_path = htmlspecialchars($backupInfo[3]);
  $backup_type = htmlspecialchars($backupInfo[4]);
  $backup_date = htmlspecialchars($backupInfo[5]);
  $backup_size = htmlspecialchars($backupInfo[6]);
  $backup_status = htmlspecialchars($backupInfo[7]);
  $backup_output = htmlspecialchars($backupInfo[8]);

  $sql = "INSERT INTO saved_backups (backup_plan, service_id, backup_server, backup_path, backup_type, backup_date, backup_size, backup_status, backup_output) VALUES ('$backup_plan_id', '$service_id', '$backup_server_id', '$backup_path', '$backup_type', '$backup_date', '$backup_size', '$backup_status', '$backup_output')";
  // Send the query to the database
  if ($conn->query($sql) === TRUE) {
    echo 'succes';
  } else {
    echo $conn->error;
  }
} else {
  // no known action specified
   die('Unknown action');
}

// close the database connection
$conn->close();
?>