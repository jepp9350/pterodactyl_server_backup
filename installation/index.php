<?php
require('./settings.php');
if(isset($_POST['db_name'])){
    # Check if db is valid.
    echo("db recieved..");
    $temp_database_host = $_POST['db_host'];
    $temp_database_name = $_POST['db_name'];
    $temp_database_user = $_POST['db_user'];
    $temp_database_user_password = $_POST['db_password'];
    $temp_db = array($temp_database_host,$temp_database_name,$temp_database_user,$temp_database_user_password);
    #print_r($temp_db);
    $db_update = check_database_connection("set_database", $temp_db);
    if($db_update == 200) {
        $message = "Connected successfully!";
        $message_color = "success";
        header('Location: #');
        exit;
    }
    if($db_update == 500) {
        $message = "Failed to connect, please look for typos.";
        $message_color = "warning";
    }
}
?>
<html>
    <head>
        <title>Pterodactyl server backups - Installation</title>
        <link rel="stylesheet" href="./css/bulma.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body>
    <div class="container mt-4">
        <div class="box">
            <form method="post" action="#">
            <?php if (isset($message)){
            echo '
            <div class="notification is-'.$message_color.'">
                <button class="delete"></button>
                '.$message.'</div>';
                  }?>
                <div class="field">
                    <h1 class="title">Start by connecting to a database.</h1>
                    <h2 class="subtitle">Pterodactyl server backups - Installation</h2>
                </div>
                <div class="field">
                    <p class="control has-icons-left has-icons-right">
                        <input name="db_host" class="input" type="text" placeholder="Database host (localhost)">
                        <span class="icon is-small is-left">
                            <i class="fa-solid fa-server"></i>
                        </span>
                        <span class="icon is-small is-right">
                        <i class="fas fa-check"></i>
                        </span>
                    </p>
                </div>
                <div class="field">
                    <p class="control has-icons-left">
                        <input name="db_name" class="input" type="text" placeholder="Database name">
                        <span class="icon is-small is-left">
                            <i class="fa-solid fa-database"></i>
                        </span>
                    </p>
                </div>
                <div class="field">
                    <p class="control has-icons-left">
                        <input name="db_user" class="input" type="text" placeholder="Database user">
                        <span class="icon is-small is-left">
                            <i class="fa-solid fa-user"></i>
                        </span>
                    </p>
                </div>
                <div class="field">
                    <p class="control has-icons-left">
                        <input name="db_password" class="input" type="text" placeholder="Database user password">
                        <span class="icon is-small is-left">
                        <i class="fas fa-lock"></i>
                        </span>
                    </p>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link">Submit</button>
                    </div>
                    <div class="control">
                        <button class="button is-link is-light">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </body>
    <footer>

    </footer>
</html>