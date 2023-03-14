<?php
session_start();
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
        <title><?=$display_name?></title>
        <link rel="stylesheet" href="./css/bulma.css">
        <link rel="stylesheet" href="./css/bulma-list.css">
        <link rel="stylesheet" href="./css/bulma-badge.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdn.jsdelivr.net/npm/bulma-toast@2.4.2/dist/bulma-toast.min.js"></script>
    </head>
    <body>
        <?php include('./hero.php');?>
        <?php include('./dashboard/overview.php');?>
    </body>
    <footer>

    </footer>
</html>