<style>

#notifications_button:not(:hover) {
    box-shadow: none;
    background-color: white;
    border-color: #dbdbdb;
}
</style>
<div class="container">
    <div class="block">
        <div class="columns mt-4">
            <div class="column">
                <div class="box">
                <!-- Notifications start-->
                <div id="notifications" data-example>
                    <div class="is-flex is-justify-content-end">
                    <div class="dropdown is-active is-right">
                        <div class="dropdown-trigger">
                        <button onclick="toggle_notifications()" id="notifications_button" class="button is-focused" aria-haspopup="true" aria-controls="dropdown-menu">
                            <span class="icon is-small"><span style="display: none;" title="Notifications" id="notifications_count" class="badge">0</span>
                            <i class="fas fa-bell" aria-hidden="true"></i> 
                            </span>
                        </button>
                        </div>
                        <div style="display: none!important;" class="dropdown-menu" id="notifications_content" role="menu">
                        <div class="dropdown-content py-0">
                            <div class="list has-overflow-ellipsis" style="width: 340px" id="notifications_list_parrent">
                            <a id="loading_notifications" class="list-item">
                                <div class="list-item-content">
                                <div class="list-item-title">Loading announcements...</div>
                                <div class="list-item-description">Please wait a moment.</div>
                                </div>

                                <div class="list-item-controls">
                                <button onclick="notification_mark_read('loading_notifications')" class="button is-light is-link">
                                    <span class="icon is-small">
                                    <i class="fas fa-check"></i>
                                    </span>
                                </button>
                                </div>
                            </a>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                <!-- Notifications end -->
                First column
                </div>
            </div>
            <div class="column">
                <div class="box">
                Server status
                <!-- Server list start -->
                <div id="servers_list_parrent" class="list has-visible-pointer-controls">

                    <div class="list-item">
                        <div class="list-item-image">
                        <figure class="image is-64x64">
                            <img class="is-rounded" src="https://via.placeholder.com/128x128.png?text=Image">
                        </figure>
                        </div>

                        <div class="list-item-content">
                        <div class="list-item-title">Loading servers...</div>
                        <div class="list-item-description">Please wait while your servers are loading.</div>
                        </div>

                        <div class="list-item-controls">
                        <div class="buttons is-right">
                            <button class="button" disabled>
                            <span class="icon is-small">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span>Create</span>
                            </button>

                            <button class="button" disabled>
                            <span class="icon is-small">
                                <i class="fas fa-ellipsis-h"></i>
                            </span>
                            </button>
                        </div>
                        </div>
                    </div>
                    </div>
                <!-- Server list end -->
                </div>
            </div>
        </div>
    </div>
</div>
<script>
// Javascript to toggle notifications drop down
function toggle_notifications(){
    if (document.getElementById("notifications_content").style.display == "none") {
        document.getElementById("notifications_content").style.display="block"; 
    } else {
        document.getElementById("notifications_content").style.display="none"; 
    }
}
// Javascript to delete a notification
function notification_mark_read(notification_uuid) {
    document.getElementById(notification_uuid).style.display="none";
    // Create an XMLHttpRequest object
    const notification_conn = new XMLHttpRequest();
    // Define a callback function
    notification_conn.onload = function() {
    // Here you can use the Data
    console.log(this.responseText);
    }
    // Send a request
    notification_conn.open("GET", "./dashboard/api/remove_notification.php?notification_id="+notification_uuid);
    notification_conn.send();
}

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
                show_server('error','Your session has expired, please sign in.');
            }
            //console.log(servers_array[server_array][0] + "title:" + servers_array[server_array][1]);
            servers_temp = servers_temp + '\
            <div class="list-item">\
                <div class="list-item-image">\
                <figure class="image is-64x64">\
                    <img class="is-rounded" src="https://via.placeholder.com/128x128.png?text=Image">\
                </figure>\
                </div>\
                <div class="list-item-content">\
                <div class="list-item-title">'+servers_array[server_array][2]+'</div>\
                <div class="list-item-description">ID: '+servers_array[server_array][0]+' IP: '+servers_array[server_array][3]+'</div>\
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
            <button class="button">\
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
    server_conn.open("GET", "./dashboard/api/sync_servers.php");
    server_conn.send();
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
    notification_conn.open("GET", "./dashboard/api/sync_notifications.php");
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
</script>