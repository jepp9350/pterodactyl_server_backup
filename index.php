<?php
session_start();
// Check to see if it's a browser refreshing.
if (isset($_GET["api_key"])) {
    $api_key = $_GET["api_key"];
    if ($api_key == "none"){
        if (!isset($_SESSION['user'])) {
            // If the request has no api key, and the user isn't logged in.
            $errors = array(array("none","Access denied","You're logged out."));
            $response = json_encode($errors);
            die($response);
        }
    } else {
        // Validate api_key here.
        $errors = array(array("none","Access denied","You're logged out."));
        $response = json_encode($errors);
        die($response);
    }
    $action = $_GET["action"];
    switch ($action) {
        case "sync_notifications":
            require './dashboard/api/sync_notifications.php';
            exit();
            break;
        case "remove_notification":
            if (isset($_GET['notification_id'])) {
                require './dashboard/api/remove_notification.php';
                exit();
            } else {
                echo "Invalid, missing notification_id!";
                exit();
            }
            exit();
            break;
        case "sync_servers":
            require './dashboard/api/sync_servers.php';
            exit();
            break;
        case "add_new_server":
            require './dashboard/api/add_new_server.php';
            exit();
            break;
        case "sync_backup_servers":
            require './dashboard/api/sync_backup_servers.php';
            exit();
            break;
        default:
            echo "Invalid API call! (".$action.") not found.";
            exit();
            break;
    }
}
if (isset($_POST['action']) && isset($_POST['secret_token'])) {
    require './api/server_refresh.php';
    exit();
}
require_once './settings.php';
require_once './functions.php';
if (file_exists('./installation/') && check_database_connection("validate","default") != "connected"){
    start_installation();
    exit();
}
if (!isset($_SESSION['user'])) {
    require './pages/login.php';
    exit();
}
?>
<html>
    <head>
        <title><?=$settings_display_name?></title>
        <link rel="stylesheet" href="./css/bulma.css">
        <link rel="stylesheet" href="./css/bulma-list.css">
        <link rel="stylesheet" href="./css/bulma-badge.min.css">
        <link rel="stylesheet" href="./css/dashboard-styles.css">
        <link rel="stylesheet" href="./css/bulma-divider.min.css">
        <link rel="stylesheet" href="https://cdn.rawgit.com/octoshrimpy/bulma-o-steps/master/bulma-steps.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdn.jsdelivr.net/npm/bulma-toast@2.4.2/dist/bulma-toast.min.js"></script>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    </head>
    <body>
        <?php include('./hero.php');?>
        <?php include('./dashboard/overview.php');?>
    </body>
    <footer>

    </footer>
</html>