<?php
if (!isset($_SESSION['user'])) {
    $errors = array(
        array("notification1","Access denied","You're logged out."));
    $response = json_encode($errors);
    die($response);
} else {
    //echo "You're logged in!";
    echo run_backup_job($_SESSION['user'],$_GET['backup_plan_id']);
    //return "Hi!";
}
#echo $response;
function run_backup_job($userid,$backup_plan_id){
    require './database.php';
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $date_and_time = date('Y-m-d H:i:s');
    // Update the backup plan last run time to now
    $sql = "UPDATE backup_plans SET backup_last_run = '$date_and_time' WHERE id = '$backup_plan_id'";
    // Send the query to the database 
    $result = $conn->query($sql);
    // Get the backup plan
    $sql = "SELECT * FROM backup_plans WHERE id = '$backup_plan_id'";
    $result = $conn->query($sql);
    // if result array is larger than 0, continue
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            // Get the backup plan
            //echo 'Initializing backup plan: '.$row["displayname"].'...';
            // Determine the type of backup to take
            switch ($row["backup_type"]) {
                case "mysql":
                    return take_mysql_backup($row["service_id"],$row["backup_server"],$row["id"]);
                    break;
                case "files":
                    return "Files is not yet supported.";
                    break;
                default:
                    return "Backup type not supported.";
                    break;
            }
        }
    } else {
        // If the backup plan does not exist, return an error
        echo "Backup plan does not exist.";
        return 500;
    }
    // Get the backup server
    //echo "test";
}
function take_mysql_backup($service_id,$backup_server_id,$backup_plan_id) {
    // Inlude functions
    include './functions.php';
    // Collect the backup server details
    // Get the database settings
    require './database.php';
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Format the result as an array
    $sql = "SELECT * FROM backup_servers WHERE id = '$backup_server_id'";
    // Send the query to the database
    $result = $conn->query($sql);
    // if result array is larger than 0, continue
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            // Get the backup server
            $backup_server = $row;
            //echo 'Backup server: '.$backup_server["displayname"];
            // Determine ssh credentials
            //echo $backup_server["ssh_username"];
            //echo $backup_server["ssh_password"];
            //echo $backup_server["ssh_port"];
            //echo $backup_server["ipaddress"];
            // Determine the backup location
            //echo $backup_server["backup_path"];
            // Connect to the backup server
            include('Net/SSH2.php');
            try {
                $host = $backup_server["ipaddress"];
                $port = $backup_server["ssh_port"];
                $user = $backup_server["ssh_username"];
                $pass = $backup_server["ssh_password"];
                // get MySQL credentials
                $sql = "SELECT * FROM backup_plans WHERE id = '$backup_plan_id'";
                $result = $conn->query($sql);
                // if result array is larger than 0, continue
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        // Get the backup plan
                        $backup_plan = $row;
                        // Determine MySQL credentials
                        $mysql_username = $backup_plan["mysql_username"];
                        $mysql_password = $backup_plan["mysql_password"];
                        $mysql_port = $backup_plan["mysql_port"];
                        $mysql_host = $backup_plan["mysql_host"];
                        // Determine the backup location
                        $mysql_backup_path = $backup_plan["backup_path"];
                        // check if the backup path is valid
                        if (substr($mysql_backup_path, -1) != "/") {
                            $mysql_backup_path = $mysql_backup_path."/";
                        }
                        // Get date and time
                        $date_and_time = date('Y-m-d-H-i-s');
                        // Determine the backup filename
                        $mysql_backup_filename = "mysql_backup_".$date_and_time.".sql";
                        // Prepare the backup folder
                        $prepare_backup_path = "mkdir -p ".$mysql_backup_path.$service_id;
                        // Determine the full backup path
                        $mysql_backup_path = $mysql_backup_path.$service_id."/".$mysql_backup_filename;
                    }
                } else {
                    // If the backup plan does not exist, return an error
                    echo "Backup plan does not exist.";
                    return 500;
                }
                $ssh = new Net_SSH2($host, $port, 10);
            } catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
            if (!$ssh->login($user, $pass)) {
                // Echo error message as a notification.
                echo '<div class="notification is-danger">
                <button class="delete"></button>
                <strong>SSH connection failed</strong> - Please check the SSH credentials for the backup server.
                </div>';
                //echo "Error: SSH connection failed.<br>Error code: 500";
                // log the error
                log_event('Backup failed','Backup failed for backup plan: '.$backup_plan["displayname"].' on backup server: '.$backup_server["displayname"].'.',$service_id,null);
                return 'ERROR: 500 - SSH connection failed.';
            } else {
                // Create backup command
                $command = "mysqldump -h ".$mysql_host." -u ".$mysql_username." --all-databases -p'".$mysql_password."' > ".$mysql_backup_path." && echo 'Backup_completed_successfully.' || echo 'Backup_failed.'";
                // Send the backup command
                $lines = explode("\n", $ssh->exec($prepare_backup_path.';'.$command));
                // Print the output
                $saved_lines_to_log = '';
                foreach ($lines as $line) {
                    // add the line to the log
                    $saved_lines_to_log .= $line;
                    if (strpos($line, 'Backup_completed_successfully.') !== false) {
                        // Get the backup size
                        $backup_size = 0;
                        $backup_size_output = explode("\n", $ssh->exec("ls -l ".$mysql_backup_path." | awk '{print $5}'"));
                        // remove the last line
                        array_pop($backup_size_output);
                        foreach ($backup_size_output as $line) {
                            // Sanitize the output
                            $line = sanitize_input($line);
                            $backup_size = $line;
                        }
                        // Set the backup status
                        if ($backup_size > 0) {
                            $backup_status = 'success';
                        } else {
                            $backup_status = 'failed';
                        }
                        // Backup completed successfully
                        //echo "\nBackup completed successfully.";
                        // log the event
                        log_event('Backup completed','Backup completed for backup plan: '.$backup_plan["displayname"].' on backup server: '.$backup_server["displayname"].' LOGS: '.$saved_lines_to_log,$service_id,null);
                        // Save the backup to the database
                        $sql = "INSERT INTO saved_backups (backup_plan, service_id, backup_server, backup_path, backup_type, backup_date, backup_size, backup_status, backup_output) VALUES ('$backup_plan_id', '$service_id', '$backup_server_id', '$mysql_backup_path', 'mysql', '$date_and_time', '$backup_size', '$backup_status', '$saved_lines_to_log')";
                        // Send the query to the database
                        $result = $conn->query($sql);
                        // Return a success code.
                        return 200;
                    } else if (strpos($line, 'Backup_failed.') !== false) {
                        // log the error
                        log_event('Backup failed','Backup failed for backup plan: '.$backup_plan["displayname"].' on backup server: '.$backup_server["displayname"].' LOGS: '.$saved_lines_to_log,$service_id,null);
                        // Backup failed
                        //echo "\nBackup failed.";
                        return 500;
                    }
                    //echo "\n".$line;
                }
            }
        }
    } else {
        // If the backup server does not exist, return an error
        echo "Backup server does not exist.";
        return 500;
    }
}
?>