<?php
if (!isset($_SESSION['user'])) {
    $errors = array(
        array("none","Access denied","You're logged out."));
    $response = json_encode($errors);
} else {
    require('./functions.php');
    echo add_new_server($_GET['server_type'],$_GET['display_name'],$_GET['server_ssh_username'],$_GET['server_ssh_password'],$_GET['server_ssh_port'],$_GET['server_backup_location']);
    #$response = json_encode($notifications);
}
function add_new_server($type,$displayname,$ssh_username,$ssh_password,$ssh_port,$backup_path) {
    require './database.php';
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection for errors
    if ($conn->connect_error) {
        echo "Failed to connect to database.";
        return 500;
    }
    switch ($type) {
        case "backup_server":
            // sanitize input
            $displayname = sanitize_input($displayname);
            // Generate server (secret_)token
            $secret_token = generate_secret_key('48');
            $sql = "INSERT INTO services (secret_token, displayname) VALUES (
                '$secret_token',
                '$displayname'
                )";
            if ($conn->query($sql) === TRUE) {
                #echo "A new server was created successfully";
                return "The server was created successfully.";
            } else {
                return "Error creating a new server: " . $conn->error;
            }
            break;
        case "backup_location":
            // sanitize input
            $displayname = sanitize_input($displayname);
            $ssh_username = sanitize_input($ssh_username);
            $ssh_password = sanitize_input($ssh_password);
            $ssh_port = sanitize_input($ssh_port);
            $backup_path = sanitize_input($backup_path);
            // Generate server (secret_)token
            $secret_token = generate_secret_key('48');
            $sql = "INSERT INTO backup_servers (secret_token, displayname, ssh_username, ssh_password, ssh_port, backup_path) VALUES (
                '$secret_token',
                '$displayname',
                '$ssh_username',
                '$ssh_password',
                '$ssh_port',
                '$backup_path'
                )";
            if ($conn->query($sql) === TRUE) {
            #echo "A new server was created successfully";
                return "The server was created successfully.";
            } else {
                return "Error creating a new server: " . $conn->error;
            }
        break;
        default:
            return "Invalid type, (".$type.") does not exist.";
    }
}
?>