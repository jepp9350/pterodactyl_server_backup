<?php
if (!isset($_SESSION['user'])) {
    $errors = array(
        array("none","Access denied","You're logged out."));
    die(json_encode($errors));
} else {
    require('./functions.php');
    echo fetch_backup_servers();
}
function fetch_backup_servers() {
    require('./database.php');
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection for errors
    if ($conn->connect_error) {
        echo "Failed to connect to database.";
        return 500;
    }
    $sql = "SELECT * FROM backup_servers";
    $result = $conn->query($sql);
    $servers = array();
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if (isset($row["id"]) && !empty($row["id"])) {
                array_push($servers,
                    array(
                        $row["id"],$row["secret_token"],$row["displayname"],$row["ipaddress"],$row["seen_date"],$row["reg_date"]
                        )
                );
            } else {
                if (count($servers) <= 0) {
                $servers = array(
                    array("0","none","You have no servers.","-","-","-")
                );
                }
            }
        }
    } else {
        $servers = array(
            array("0","none","You have no servers.","-","-","-")
        );
    }
    $conn->close();
    return json_encode($servers);
}
?>