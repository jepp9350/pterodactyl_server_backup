<?php
session_start();
if (!isset($_SESSION['user'])) {
    $errors = array(
        array("notification1","Access denied","You're logged out."));
    $response = json_encode($errors);
} else {
    echo fetch_notifications($_SESSION['user']);
    /*$notifications = array(
        array("notification1","Title for - 1","A loooong description here.."),
        array("notification2",15,13),
        array("notification3",5,2),
        array("notification4",17,15)
    );*/
    $notifications = '[["notification1","Title for - 1","A loooong description here.."],["notification2",15,13],["notification3",5,2],["notification4",17,15]]';
    $response = json_decode($notifications);
    #$response = json_encode($notifications);
}
#echo $response;
function fetch_notifications($userid){
    require '../../database.php';
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM accounts WHERE id='$userid'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        return $row["notifications"];
    }
    } else {
    return "0 results";
    }
    $conn->close();
}
?>