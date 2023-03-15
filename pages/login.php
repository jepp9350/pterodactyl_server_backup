<?php
if (isset($_POST['action'])){
    #echo $_POST['action'];
    switch (validate_login($_POST['email'],$_POST['password'])){
        case 200:
            //Valid credentials, refresh page.
            header('Location: #');
            break;
        case 401:
            //Invalid credentials, show error message.
            $message = "Incorrect email or password.";
            $message_color = "warning";
            break;
        default:
            //Invalid response, indicate error.
            $message = "An error has occured!";
            $message_color = "danger";
            break;
    }
}
function validate_login($email,$password){
    require './database.php';
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection
    if ($conn->connect_error) {
        return 
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM accounts WHERE email='$email'";
    $result = $conn->query($sql);
    // Prepare variables to log.
    $login_ip = $_SERVER['REMOTE_ADDR'];
    $login_email = $_POST['email'];
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if ($row["password"] == $password) {
                unset($_POST['action']);
                $_SESSION['user'] = $row["id"];
                $user = $_SESSION['user'];
                $timestamp = date("Y-m-d H:i:s");
                $sql = "UPDATE accounts SET seen_date='$timestamp' WHERE id=". $_SESSION['user'] ."";
                $result = $conn->query($sql);
                log_event("USER_LOGIN_SUCCESS", "($login_ip) signed in as ($login_email).", "none", "$user");
                return 200;
            } else {
                // In case of a wrong password.
                log_event("USER_LOGIN_FAILED_ATTEMPT_PASSWORD", "($login_ip) failed to login as ($login_email).", "none", $login_ip);
                return 401;
            }
        }
    } else {
        // In case the user doesn't exist.
        log_event("USER_LOGIN_FAILED_INVALID_ACCOUNT", "($login_ip) failed to login as ($login_email).", "none", $login_ip);
        return 401;
    }
    $conn->close();
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
    <div class="container mt-4">
        <div class="box">
            <form method="post" action="<?=$install_url?>">
                <div class="field">
                    <h1 class="title">Sign in to your account.</h1>
                    <h2 class="subtitle">Pterodactyl server backups</h2>
                </div>
                <?php if (isset($message)){echo '
            <div class="field">
                <div class="notification is-'.$message_color.'">
                    <button class="delete"></button>
                    '.$message.'
                </div>
            </div>';
            }?>
                <div class="field">
                    <p class="control has-icons-left has-icons-right">
                        <input name="email" class="input" type="email" placeholder="johndoe@example.com" required>
                        <span class="icon is-small is-left">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <span class="icon is-small is-right">
                            <i class="fas fa-check"></i>
                        </span>
                    </p>
                </div>
                <div class="field">
                    <p class="control has-icons-left">
                        <input name="password" class="input" type="password" placeholder="Hunter02" required>
                        <span class="icon is-small is-left">
                        <i class="fas fa-lock"></i>
                        </span>
                    </p>
                </div>
                <div class="field is-grouped">
                    <div class="control">
                        <button name="action" value="login_form" class="button is-link">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    </body>
</html>