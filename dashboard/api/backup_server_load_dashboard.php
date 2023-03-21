<?php
if (!isset($_SESSION['user'])) {
    $errors = array(
        array("none","Access denied","You're logged out."));
    die(json_encode($errors));
} else {
    require('./functions.php');
    // Get the backup servers login credentials
    require('./database.php');
    // Create connection
    $conn = new mysqli($database_host, $database_user, $database_user_password, $database_name);
    // Check connection for errors
    if ($conn->connect_error) {
        echo "Failed to connect to database.";
        return 500;
    }
    // Get the backup servers login credentials from the database and store them in variables for later use
    $sql = "SELECT ipaddress, ssh_username, ssh_password, ssh_port, backup_path, displayname FROM backup_servers WHERE id = '".$_GET['backup_server_id']."'";
    // Run the query
    $result = $conn->query($sql);
    // If there is a result
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            // Store the data in variables
            $backup_server_ip = $row["ipaddress"];
            $backup_server_ssh_username = $row["ssh_username"];
            $backup_server_ssh_password = $row["ssh_password"];
            $backup_server_ssh_port = $row["ssh_port"];
            $backup_server_backup_path = $row["backup_path"];
            $backup_server_displayname = $row["displayname"];
        }
    } else {
        echo "No results";
    }
    echo get_server_dashboard_info($_GET['backup_server_id'], $backup_server_ip, $backup_server_ssh_username, $backup_server_ssh_password, $backup_server_ssh_port, $backup_server_backup_path, $backup_server_displayname);
}
function get_server_dashboard_info($server_id, $backup_server_ip, $backup_server_ssh_username, $backup_server_ssh_password, $backup_server_ssh_port, $backup_server_backup_path, $backup_server_displayname) {
    $server_storage_locations = get_server_storage($backup_server_ip, $backup_server_ssh_username, $backup_server_ssh_password, $backup_server_ssh_port);
    // Check if the server is online
    if (str_contains($server_storage_locations,"500")) {
        die;
    }
    echo '
    <div class="column is-12">
    <div class="divider">Server information</div>
    <!-- Server information -->
    <div class="columns is-multiline">
        <!-- Server name -->
        <div class="column is-half">
            <div class="field">
                <label class="label">Server name</label>
                <div class="control">
                    <input class="input" type="text" placeholder="Server name" value="'.$backup_server_displayname.'" disabled>
                </div>
                <p class="help">This is the friendly display name of the server.</p>
            </div>
        </div>
        <!-- End of server name -->
        <!-- Server IP -->
        <div class="column is-half">
            <div class="field">
                <label class="label">Server IP</label>
                <input class="input" type="text" placeholder="Server ip" value="'.$backup_server_ip.'" disabled>
                <p class="help">This is the IP address/hostname of the server.</p>
            </div>
        </div>
        <!-- End of server IP -->
        <!-- Server backup path -->
        <div class="column is-half">
            <div class="field">
                <label class="label">Server backup path</label>
                <input class="input" type="text" placeholder="Server backup path" value="'.$backup_server_backup_path.'" disabled>
                <p class="help">This is the path where the backups are stored.</p>
            </div>
        </div>
        <!-- End of server backup path -->
        <!-- Server max backup size -->
        <div class="column is-half">
            <div class="field">
                <label class="label">Server max backup size</label>
                <input class="input" type="text" placeholder="Server max backup size" value="1TB" disabled>
                <p class="help">This is the maximum size of the backups.</p>
            </div>
        </div>
        <!-- End of server max backup size -->
    </div>
    <!-- End of server information -->
    </div>
<div class="column is-12">
<div class="divider">Manage server</div>
<!-- Manage server -->
<div class="buttons">
    <button class="button is-small is-info">
    <span class="icon is-small">
        <i class="fas fa-play"></i>
    </span>
    <span>Start</span>
    </button>
    <button class="button is-small is-warning">
        <span class="icon is-small">
            <i class="fas fa-pause"></i>
        </span>
        <span>Pause</span>
    </button>
    <button class="button is-small is-danger">
        <span class="icon is-small">
            <i class="fas fa-stop"></i>
        </span>
        <span>Stop</span>
    </button>
</div>
<!-- End of manage server -->
</div>
<div class="column is-12">
<div class="divider">Server storage</div>
<!-- Server storage -->
'. $server_storage_locations .'
<!-- End of server storage -->
</div>
</div>
</div>';}
?>
<?php
// Function to get the servers storage usage
function get_server_storage($host, $user, $pass, $port) {
    set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
    // Check if the values are empty
    if (empty($host) || empty($user) || empty($pass) || empty($port)) {
        // Echo error message as a notification.
        echo '<div class="notification is-danger">
        <button class="delete"></button>
        <strong>Missing SSH credentials</strong> - Please enter the SSH credentials for the backup server.
      </div>';
        echo "Error: Empty values - SSH connection failed.<br>Error code: 500";
        return 'ERROR: 500 - SSH credentials missing.';
    }

    include('Net/SSH2.php');
    try {
        $ssh = new Net_SSH2($host, $port, 10);
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    if (!$ssh->login($user, $pass)) {
        // Echo error message as a notification.
        echo '<div class="notification is-danger">
        <button class="delete"></button>
        <strong>SSH connection failed</strong> - Please check the SSH credentials for the backup server.
        </div>';
        echo "Error: SSH connection failed.<br>Error code: 500";
        return 'ERROR: 500 - SSH connection failed.';
    } else {
        //echo "Login successfull<br>";
        //echo "Current directory: ";
        //echo $ssh->exec('pwd');
        //echo "<br>Disk usage:<br>";
        //Split by line
        $lines = explode("\n", $ssh->exec('df -h'));
        // Make sure the first line is removed
        array_shift($lines);
        // Number of lines
        $total_lines_count = 0;
        // Create the columns multiline div
        $full_result = '';
        $all_columns = '';
        //Loop through lines
        foreach ($lines as $line) {
            //Split by space
            $words = explode(" ", $line);
            //Loop through words with a counter of 0
            $counter = 0;
            $column_variables = array();
            //Array of item formats
            $formats = array("Filesystem", "Size", "Used", "Available", "Storage_used", "Mounted_on");
            foreach ($words as $word) {

                //If word is not empty
                if (!empty($word)) {
                    // Save the word as a variable named after format
                    $column_variables[$formats[$counter]] = $word;
                    // print array
                    //print_r($column_variables);
                    $counter = $counter + 1;
                }
            }
            // Print the content of the column, if not empty
            if (isset($column_variables['Filesystem']) && $counter != 0) {
                // Foreach column_variables, create a variable with the name of the key and the value of the value
                foreach ($formats as $format_display) {
                    // Create the variable
                    if (!isset($column_variables[$format_display])) {
                        $column_variables[$format_display] = "Undefined.";
                    }
                }
                    // Check if the file system is tmpfs, if so, don't print it
                if ($column_variables['Filesystem'] == "tmpfs" OR $column_variables['Filesystem'] == "udev" OR $column_variables['Mounted_on'] == "Undefined.") {
                    // Don't print the column
                } else {
                    // Create the column content - call the function to create the column
                    $column_content = create_backup_server_overview_storage_element($column_variables['Filesystem'], $column_variables['Size'], $column_variables['Used'], $column_variables['Available'], $column_variables['Storage_used'], $column_variables['Mounted_on']);
                    // Save the column
                    $all_columns .= $column_content;
                    // Add to total lines count
                    $total_lines_count = $total_lines_count + 1;
                    }
            } else {
                // If the column is empty, don't print it
                //echo "Empty column";
            }
            $counter = 0;
        }
        // Create the columns multiline div
        $full_result .= '<div class="columns is-multiline">';
        // Add the columns to the full result
        $full_result .= $all_columns;
        // Close the columns multiline div
        $full_result .= '</div>';
        // Add the total lines count to the full result (bottom of the page)
        $full_result .= '<p>Total <span class="has-tooltip-right has-tooltip-multiline" data-tooltip="Some drives are hidden, to avoid showing irrelavant data.">(visible)</span> storage locations: ' . $total_lines_count . '</p>';
        // Echo the full result
        return $full_result;
    }
}
function create_backup_server_overview_storage_element($column_Filesystem = "none", $column_Size = "none", $column_Used = "none", $column_Available = "none", $column_Storage_used = "none", $column_Mounted_on = "none"){
    // check if the array is empty
    if (empty($column_Filesystem) || empty($column_Size) || empty($column_Used) || empty($column_Available) || empty($column_Storage_used) || empty($column_Mounted_on)) {
        // Echo error message as a notification.
        echo '<div class="notification is-danger">
        <button class="delete"></button>
        <strong>Column variables array is empty</strong> - Please check the storage box function.
      </div>';
        //echo "Error: Empty values - Storage box.<br>Error code: ";
        
    } else {
    // Create the column content
    $column_centent_temp = '';
    // Create the column box and div
    $column_centent_temp .= '<div class="column is-half">';
    $column_centent_temp .= '<div class="box">';
    // Create the column title
    $column_centent_temp .= '<div class="title is-5">
    <span class="icon-text">
    <span class="icon">
      <i class="fa-regular fa-hard-drive"></i>
  </span></span> ' . $column_Mounted_on . '</div>';
    // Create the column statistics
    $column_centent_temp .= '<div class="columns is-multiline">';
    $column_centent_temp .= '<div class="column is-half">';
    $column_centent_temp .= '<div class="title is-6">Size</div>';
    $column_centent_temp .= '<div class="subtitle is-6">' . $column_Size . '</div>';
    $column_centent_temp .= '</div>';
    $column_centent_temp .= '<div class="column is-half">';
    $column_centent_temp .= '<div class="title is-6">Used</div>';
    $column_centent_temp .= '<div class="subtitle is-6">' . $column_Used . '</div>';
    $column_centent_temp .= '</div>';
    $column_centent_temp .= '<div class="column is-half">';
    $column_centent_temp .= '<div class="title is-6">Available</div>';
    $column_centent_temp .= '<div class="subtitle is-6">' . $column_Available . '</div>';
    $column_centent_temp .= '</div>';
    $column_centent_temp .= '<div class="column is-half">';
    $column_centent_temp .= '<div class="title is-6">Storage used</div>';
    $column_centent_temp .= '<div class="subtitle is-6">' . $column_Storage_used . '</div>';
    $column_centent_temp .= '</div>';
    $column_centent_temp .= '</div>';
    // Create the progress bar color based on the storage used
    if (str_replace("%", "", $column_Storage_used) >= 90) {
        $column_centent_temp .= '<progress class="progress is-danger" value="' . str_replace("%", "", $column_Storage_used) . '" max="100">' . $column_Storage_used . '</progress>';
    } elseif (str_replace("%", "", $column_Storage_used) >= 80) {
        $column_centent_temp .= '<progress class="progress is-warning" value="' . str_replace("%", "", $column_Storage_used) . '" max="100">' . $column_Storage_used . '</progress>';
    } else {
        $column_centent_temp .= '<progress class="progress is-primary" value="' . str_replace("%", "", $column_Storage_used) . '" max="100">' . $column_Storage_used . '</progress>';
    }

    // Close the column box and div
    $column_centent_temp .= '</div>';
    $column_centent_temp .= '</div>';

    // Return the column content
    //echo $column_centent_temp;
    return $column_centent_temp;
    }

}
?>