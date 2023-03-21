// Javascript => Backend => Refresh servers.
function server_sync(){
    // Create an XMLHttpRequest object
    const server_conn = new XMLHttpRequest();

    // Define a callback function
    server_conn.onload = function() {
    // Here you can use the Data
    server_list_parrent = document.getElementById("servers_list_parrent");
    // remove old servers
    server_list_parrent.innerHTML = '';
    // add new servers
    servers_array = JSON.parse(this.responseText);
    console.log(this.responseText);
    servers_temp = '';
    if (servers_array[0][0] != "0") {
        for (var server_array in servers_array) {
            if(servers_array[server_array][1] == "Access denied") {
                show_notification('error','Your session has expired, please sign in.');
                endfor;
            }
            //console.log(servers_array[server_array][0] + "title:" + servers_array[server_array][1]);
            servers_temp = servers_temp + '\
            <div class="list-item">\
                <div class="list-item-image">\
                <figure class="image is-64x64">\
                    <img style="background-color: #00d1b2;" class="is-rounded" src="./src/img/server_transferring_icon.png">\
                </figure>\
                </div>\
                <div class="list-item-content">\
                <div class="list-item-title">'+servers_array[server_array][2]+' <span class="tag is-normal is-info is-light is-rounded">Server</span></div>\
                <div class="list-item-description">ID: '+servers_array[server_array][0]+' IP: '+servers_array[server_array][3]+' Last seen: '+servers_array[server_array][4]+' Reg: '+servers_array[server_array][5]+'</div>\
                </div>\
                <div class="list-item-controls">\
                <div class="buttons is-right">\
                    <button class="button">\
                    <span class="icon is-small">\
                        <i class="fas fa-edit"></i>\
                    </span>\
                    <span>Edit</span>\
                    </button>\
                    <button class="button">\
                    <span class="icon is-small">\
                        <i class="fas fa-ellipsis-h"></i>\
                    </span>\
                    </button>\
                </div>\
                </div>\
            </div>';
        }
    }
    // Add the "add new server" entry to the server list.
    servers_temp = servers_temp + '\
    <div class="list-item">\
        <div class="list-item-image">\
        <figure class="image is-64x64">\
            <img style="background-color: #00d1b2;" class="is-rounded" src="./src/img/logo_square.png">\
        </figure>\
        </div>\
        <div class="list-item-content">\
        <div class="list-item-title">Add a new server.</div>\
        <div class="list-item-description">Start backing up more data!</div>\
        </div>\
        <div class="list-item-controls">\
        <div class="buttons is-right">\
            <button class="button" onclick="show_modal(\'create-new-server\')">\
            <span class="icon is-small">\
                <i class="fas fa-edit"></i>\
            </span>\
            <span>Create</span>\
            </button>\
            <button class="button" disabled>\
            <span class="icon is-small">\
                <i class="fas fa-ellipsis-h"></i>\
            </span>\
            </button>\
        </div>\
        </div>\
    </div>\
    </div>';
    servers_list_parrent.innerHTML = servers_temp;
    }

    // Send a request
    server_conn.open("GET", "./index.php?api_key=none&action=sync_servers");
    server_conn.send();
}
// Javascript => Backend => Refresh backup servers.
function backup_server_sync(){
    // Create an XMLHttpRequest object
    const backup_server_conn = new XMLHttpRequest();
    backup_server_conn.onload = function() {
    // Here you can use the Data
    backup_server_list_parrent = document.getElementById("add_new_server_field_server_location");
    backup_servers_list_parrent_overview = document.getElementById("backup_servers_list_parrent_overview");
    // remove old backup servers
    backup_server_list_parrent.innerHTML = '';
    //backup_servers_list_parrent_overview.innerHTML = '';
    // add new backup servers
    backup_servers_array = JSON.parse(this.responseText);
    backup_servers_temp = '';
    backup_servers_list_temp = '';
    backup_servers_temp = backup_servers_temp + '\
    <option selected value="default">Location (where to store the backups)</option>';
    if (backup_servers_array[0][0] != "0") {
        for (var backup_server_array in backup_servers_array) {
            if(backup_servers_array[backup_server_array][1] == "Access denied") {
                show_notification('error','Your session has expired, please sign in.');
                endfor;
            }
            //console.log(backup_servers_array[backup_server_array][0] + "title:" + backup_servers_array[backup_server_array][1]);
            // Add the backup server to the "add new server" field.
            backup_servers_temp = backup_servers_temp + '\
            <option value="'+backup_servers_array[backup_server_array][0]+'">'+backup_servers_array[backup_server_array][2]+'</option>';
            // Set default image for the backup server.
            backup_server_image = "./src/img/server_transferring_icon.png";
            backup_server_image_color = "#00d1b2";
            // Check when the backup server was last seen.
            if(backup_servers_array[backup_server_array][4] == null) {
                backup_servers_array[backup_server_array][4] = "Never";
                backup_server_image = "./src/img/server_transferring_icon.png";
                backup_server_image_color = "#ff3860";

            }
            // Check what the backup server's ip is.
            if(backup_servers_array[backup_server_array][3] == null) {
                backup_servers_array[backup_server_array][3] = "Unknown";
            }
            // check if the backup server already is in the overview.
            if(!!document.getElementById('server_manage_id_'+backup_servers_array[backup_server_array][0]+'')) {

                // Check if the backup server manager is active.
                if (document.getElementById('server_manage_id_'+backup_servers_array[backup_server_array][0]+'').style.display != "none") {
                    is_displayed = "block";
                } else {
                    is_displayed = "none";
                }
            } else {
                is_displayed = "none";
                // log that it was not found.
                console.log("Server not found: "+backup_servers_array[backup_server_array][0]);
            }
            // Add the backup server to the overview.
            backup_servers_list_temp = backup_servers_list_temp + '\
            <div class="list-item">\
                <div class="list-item-image">\
                <figure class="image is-64x64">\
                    <img style="background-color: '+backup_server_image_color+';" class="is-rounded" src="'+backup_server_image+'">\
                </figure>\
                </div>\
                <div class="list-item-content">\
                <div class="list-item-title">'+backup_servers_array[backup_server_array][2]+' <span class="tag is-normal is-info is-light is-rounded">Backup location</span></div>\
                <div class="list-item-description">ID: '+backup_servers_array[backup_server_array][0]+' IP: '+backup_servers_array[backup_server_array][3]+' Last seen: '+timeSince(backup_servers_array[backup_server_array][4])+' Reg: '+backup_servers_array[backup_server_array][5]+'</div>\
                <div class="columns control_server_dashboard" style="display:'+is_displayed+';" id="server_manage_id_'+backup_servers_array[backup_server_array][0]+'">\
                <div class="column is-12">\
                    <div class="divider">Manage server</div>\
                    <!-- Manage server -->\
                    <div class="buttons">\
                        <button class="button is-small is-info">\
                        <span class="icon is-small">\
                            <i class="fas fa-play"></i>\
                        </span>\
                        <span>Start</span>\
                        </button>\
                        <button class="button is-small is-warning">\
                            <span class="icon is-small">\
                                <i class="fas fa-pause"></i>\
                            </span>\
                            <span>Pause</span>\
                        </button>\
                        <button class="button is-small is-danger">\
                            <span class="icon is-small">\
                                <i class="fas fa-stop"></i>\
                            </span>\
                            <span>Stop</span>\
                        </button>\
                    </div>\
                    <!-- End of manage server -->\
                </div>\
                <div class="column is-12">\
                    <div class="divider">Server storage</div>\
                    <!-- Server storage -->\
                    <div class="columns">\
                        <div class="column">\
                            <p class="is-6">Backup storage: 60%</p>\
                            <progress class="progress is-success" value="60" max="100">60%</progress>\
                        </div>\
                        <div class="column">\
                            <p class="is-6">Total storage: 60%</p>\
                            <progress class="progress is-warning" value="60" max="100">60%</progress>\
                        </div>\
                    </div>\
                    <!-- End of server storage -->\
                </div>\
                <div class="column is-12">\
                    <div class="divider">Server information</div>\
                    <!-- Server information -->\
                    <div class="columns">\
                        <div class="column">\
                            <div class="field">\
                                <label class="label">Server name</label>\
                                <div class="control">\
                                    <input class="input" type="text" placeholder="Server name">\
                                </div>\
                                <p class="help">This is the name of the server.</p>\
                            </div>\
                        </div>\
                    </div>\
                    <!-- End of server information -->\
                    </div>\
                </div>\
            </div>\
                <div class="list-item-controls">\
                <div class="buttons is-right">\
                    <button class="button">\
                    <span class="icon is-small">\
                        <i class="fas fa-edit"></i>\
                    </span>\
                    <span>Edit</span>\
                    </button>\
                    <button onclick="toggle_backup_server_manager('+backup_servers_array[backup_server_array][0]+')" class="button">\
                    <span class="icon is-small">\
                        <i class="fas fa-ellipsis-h"></i>\
                    </span>\
                    </button>\
                </div>\
                </div>\
            </div>';
        }
    }
    backup_server_list_parrent.innerHTML = backup_servers_temp;
    backup_servers_list_parrent_overview.innerHTML = backup_servers_list_temp;
    }
    // Send a request
    backup_server_conn.open("GET", "./index.php?api_key=none&action=sync_backup_servers");
    backup_server_conn.send();
}
// Javascript => Backend => Toggle the backup server manager.
function toggle_backup_server_manager(backup_server_id) {
    // toggle it
    modal = document.getElementById('server_overview_manage');
    // check if the modal is active.
    if (modal.classList.contains('is-active') === true) {
        // if it is active, close it.
        modal.classList.remove('is-active');
    } else {
        // if it is not active, open it.
        modal.classList.add('is-active');
    }
    // Get the server information.
    // Create an XMLHttpRequest object
    const backup_server_overview_conn = new XMLHttpRequest();
    backup_server_overview_conn.onload = function() {
        // Here you can use the Data
        server_overview_manage_body.innerHTML =this.responseText;


    // Send a request
}
    backup_server_overview_conn.open("GET", "./index.php?api_key=none&action=backup_server_load_dashboard&backup_server_id="+backup_server_id);
    backup_server_overview_conn.send();

    /*
    if(document.getElementById('server_manage_id_'+backup_server_id).style.display == "none") {
        document.getElementById('server_manage_id_'+backup_server_id).style.display = "block";
    } else {
        document.getElementById('server_manage_id_'+backup_server_id).style.display = "none";
    }*/
}
// Javascript => Backend => Refresh notifications.
function notification_sync(){
    // Create an XMLHttpRequest object
    const notification_conn = new XMLHttpRequest();

    // Define a callback function
    notification_conn.onload = function() {
    // Here you can use the Data
    notifications_list_parrent = document.getElementById("notifications_list_parrent");
    // remove old notifications
    notifications_list_parrent.innerHTML = '';
    // add new notifications
    notifications_array = JSON.parse(this.responseText);
    //console.log(this.responseText);
    notifications_temp = '';
    if (notifications_array[0][0] == "notification_none") {
        document.getElementById("notifications_count").innerHTML = 0;
        document.getElementById("notifications_count").style.display = "none";
    } else {
        document.getElementById("notifications_count").innerHTML = notifications_array.length;
        document.getElementById("notifications_count").style.display = "block";
    }
    for (var notification_array in notifications_array) {
        if(notifications_array[notification_array][1] == "Access denied") {
            show_notification('error','Your session has expired, please sign in.');
        }
        //console.log(notifications_array[notification_array][0] + "title:" + notifications_array[notification_array][1]);
        notifications_temp = notifications_temp + '\
        <a id="'+notifications_array[notification_array][0]+'" class="list-item">\
            <div class="list-item-content">\
                <div class="list-item-title">'+notifications_array[notification_array][1]+'</div>\
                <div class="list-item-description">'+notifications_array[notification_array][2]+'</div>\
            </div>\
            <div class="list-item-controls">\
                <button onclick="notification_mark_read(\''+notifications_array[notification_array][0]+'\')" class="button is-light is-link">\
                    <span class="icon is-small">\
                        <i class="fas fa-check"></i>\
                    </span>\
                </button>\
            </div>\
        </a>';
    }
    notifications_list_parrent.innerHTML = notifications_temp;
    }

    // Send a request
    //notification_conn.open("GET", "./dashboard/api/sync_notifications.php");
    notification_conn.open("GET", "./index.php?api_key=none&action=sync_notifications");
    notification_conn.send();
}
// Javascript => Every 1 second => Run functions.
function refresh_secondly(){
setInterval(everySecondFunction, 5000);
}
refresh_secondly();

function everySecondFunction() {
// stuff you want to do every second
    show_notification('syncing','Refreshing...');
    notification_sync();
    server_sync();
    backup_server_sync();
}
function show_notification(type,message){
    switch(type){
        case 'syncing':
            //bulmaToast.toast({ message: message, position: 'bottom-right', type: 'is-success', opacity: 0.8, duration: 500 });
            break;
        case 'error':
            bulmaToast.toast({ message: message, position: 'bottom-right', type: 'is-warning', opacity: 0.8, duration: 5000 });
            break;
    }
}
everySecondFunction();
// Javascript to the backup server modal.
function show_modal_backup_server(id) {
    // Define variables
    display_name = document.getElementById('add_new_server_field_display_name').value;
    server_type = document.getElementById('add_new_server_field_server_type').value;
    switch(server_type) {
        case "backup_server":
            // Set variables
            //server_backup_location = document.getElementById('add_new_server_field_server_location').value;
            break;
        case "backup_location":
            // Set variables
            if (document.getElementById('add_new_server_field_server_backup_location').value == "custom") {
                server_backup_location = document.getElementById('add_new_server_field_server_backup_location_custom').value;
            } else {
                server_backup_location = document.getElementById('add_new_server_field_server_backup_location').value;
            }
            server_ssh_username = document.getElementById('add_new_server_field_server_ssh_username').value;
            server_ssh_password = document.getElementById('add_new_server_field_server_ssh_password').value;
            server_ssh_port = document.getElementById('add_new_server_field_server_ssh_port').value;
            break;

    }
}