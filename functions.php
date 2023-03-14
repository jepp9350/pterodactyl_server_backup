<?php
function start_installation (){
    include './installation/index.php';
}
function check_database_connection($action, $database){
    if (file_exists("./database.php")) {
        #echo "Database file exists.";
    } else {
        $database_file = fopen("./database.php", "w") or die("Unable to open file!");
        $database_file_default = "<?php\n\$database_host = 'localhost';\n\$database_name = 'none';\n\$database_user = 'db_user';\n\$database_user_password = 'db_user_password';\n?>";
        fwrite($database_file, $database_file_default);
        fclose($database_file);
    }
    switch ($action) {
        case "validate":
            require './database.php';
            if($database_name == "none"){
                return "db_not_set.";
            }
            #If database has been set:
            else {
                require './database.php';
                $database_arr = array($database_host,$database_name,$database_user,$database_user_password);
                $database_status = check_database_connection('set_database', $database_arr);
                if (isset($database_status)){
                    if ($database_status == 200) {
                        return "connected";
                    } else {
                        return "error";
                    }
                } else {
                    return "error";
                }
            }
            break;
        case "set_database":
            if (is_array($database)){
                #Is array, test here.
                #print_r($database);
                $temp_database_host = $database['0'];
                $temp_database_name = $database['1'];
                $temp_database_user = $database['2'];
                $temp_database_user_password = $database['3'];

                // Create connection
                try {
                    $conn = new mysqli($temp_database_host, $temp_database_user, $temp_database_user_password, $temp_database_name);
                    // Check connection for errors
                    if ($conn->connect_error) {
                        echo "Failed 1st check.";
                        return 500;
                    } else {
                        // If there's no errors, save the new database as database.php & initate database table setup.
                        $database_file = fopen("./database.php", "w") or die("Unable to open file!");
                        $database_file_new = "<?php\n\$database_host = '$temp_database_host';\n\$database_name = '$temp_database_name';\n\$database_user = '$temp_database_user';\n\$database_user_password = '$temp_database_user_password';\n?>";
                        fwrite($database_file, $database_file_new);
                        fclose($database_file);
                        // Create tables, if they don't exist:
                        // Checking ACCOUNT table.
                        try {
                            // sql to create "accounts" table
                            $sql = "CREATE TABLE IF NOT EXISTS accounts (
                                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                firstname VARCHAR(30) NOT NULL,
                                lastname VARCHAR(30) NOT NULL,
                                email VARCHAR(50),
                                password VARCHAR(255),
                                notifications VARCHAR(255),
                                seen_date VARCHAR(255),
                                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                                )";
                            if ($conn->query($sql) === TRUE) {
                                #echo "Table accounts created successfully";
                            } else {
                                echo "Error creating table: " . $conn->error;
                            }
                            // Default notifications:
                            $notifications = array(
                                array("welcome_admin","Welcome to your new backup panel.","Start by adding your first server.")
                            );
                            $default_notifications = json_encode($notifications);
                            $sql = "INSERT IGNORE INTO accounts (id, firstname, lastname, email, password, notifications) VALUES (
                                '1',
                                'Admin',
                                'User',
                                'notset',
                                'password',
                                '".$default_notifications."')";
                            if ($conn->query($sql) === TRUE) {
                                #echo "Admin default account created successfully";
                            } else {
                                echo "Error creating default admin account: " . $conn->error;
                            }
                        }
                        //catch exception
                        catch(Exception $e) {
                            if (strpos($e,"already exists")) {
                                echo "accounts table already exists.";
                            } else {
                                echo "Couldn't create services table, error: ".$e;
                            }
                        }
                        // Checking services table.
                        try {
                            // sql to create "services" table
                            $sql = "CREATE TABLE IF NOT EXISTS services (
                                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                secret_token VARCHAR(255) NOT NULL,
                                displayname VARCHAR(255) NOT NULL,
                                ipaddress VARCHAR(255),
                                seen_date VARCHAR(255),
                                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                                )";
                                
                                if ($conn->query($sql) === TRUE) {
                                #echo "Table services created successfully";
                                } else {
                                echo "Error creating table: " . $conn->error;
                                }
                        }
                        //catch exception
                        catch(Exception $e) {
                            if (strpos($e,"already exists")) {
                                echo "services table already exists.";
                            } else {
                                echo "Couldn't create services table, error: ".$e;
                            }
                        }
                        // Checking server_logs table.
                        try {
                            // sql to create "server_logs" table
                            $sql = "CREATE TABLE IF NOT EXISTS server_logs (
                                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                event VARCHAR(255) NOT NULL,
                                description VARCHAR(255),
                                service VARCHAR(255),
                                user VARCHAR(255),
                                reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                                )";
                                
                                if ($conn->query($sql) === TRUE) {
                                #echo "Table server_logs created successfully";
                                } else {
                                echo "Error creating table: " . $conn->error;
                                }
                        }
                        //catch exception
                        catch(Exception $e) {
                            if (strpos($e,"already exists")) {
                                echo "server_logs table already exists.";
                            } else {
                                echo "Couldn't create server_logs table, error: ".$e;
                            }
                        }
                        $conn->close();
                        return 200;
                        }
                }
                //catch exception
                catch(Exception $e) {
                    echo "Failed 2st check.".$e;
                    return 500;
                }
            } else {
                return "db_not_set.";
            }
            break;
        default:
            return("Invalid action for check_database_connection function!");
            break;
    }
}
function log_event($event, $description, $service, $user) {
    require './database.php';
    $database_arr = array($database_host,$database_name,$database_user,$database_user_password);
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO server_logs (event, description, service, user)
    VALUES ('$event', '$description', '$service', '$user')";

    if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
    } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
function fetch_notifications($userid){
    require './database.php';
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