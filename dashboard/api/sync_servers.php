<?php
session_start();
if (!isset($_SESSION['user'])) {
    $errors = array(
        array("server1","Access denied","You're logged out."));
    $response = json_encode($errors);
} else {
    echo fetch_servers();
    /*$servers = array(
        array("server1","Title for - 1","A loooong description here.."),
        array("server2",15,13),
        array("server3",5,2),
        array("server4",17,15)
    );*/
    $servers = '[["server1","Title for - 1","A loooong description here.."],["server2",15,13],["server3",5,2],["server4",17,15]]';
    $response = json_decode($servers);
    #$response = json_encode($servers);
}
#echo $response;
function fetch_servers(){
    require '../../database.php';
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM services";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if (isset($row["id"]) && !empty($row["id"])) {
                return json_encode($row);
            } else {
                $servers = array(
                    array("0","none","You have no servers.","-","-","-")
                );
                return json_encode($servers);
            }
        }
    } else {
        $servers = array(
            array("0","none","You have no servers.","-","-","-")
        );
        return json_encode($servers);
    }
    $conn->close();
}
?>