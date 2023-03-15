<?php
if (!isset($_SESSION['user'])) {
    $errors = array(
        array("notification1","Access denied","You're logged out."));
    $response = json_encode($errors);
} else {
    echo remove_notification($_SESSION['user'],$_GET['notification_id']);
}
#echo $response;
function remove_notification($userid,$notification_id){
    require './database.php';
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM accounts WHERE id='$userid'";
    $result = $conn->query($sql);
    $notifications_new = array();
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if (isset($row["notifications"]) && !empty($row["notifications"])) {
                $notifications = json_decode($row["notifications"]);
                foreach ($notifications as $notification) {
                    if ($notification[0] != $notification_id) {
                        array_push($notifications_new,$notification);
                    }
                }
                if (count($notifications_new,0) <= 0) {
                    $sql = "UPDATE accounts SET notifications=NULL WHERE id=". $_SESSION['user'] ."";
                } else {
                    $sql = "UPDATE accounts SET notifications='". json_encode($notifications_new) ."' WHERE id=". $_SESSION['user'] ."";
                }
                $update_result = $conn->query($sql);
                
            }
        }
        $conn->close();
    }
}
?>