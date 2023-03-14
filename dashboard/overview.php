<?php
$notifications = array(
    array("notification1","Title for - 1","A loooong description here.."),
    array("notification2",15,13),
    array("notification3",5,2),
    array("notification4",17,15)
);
?>
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
                            <?php if (isset($notifications)){echo('<span class="icon is-small"><span title="Notifications" class="badge">'. count($notifications,0) .'</span>');}?>
                            <i class="fas fa-bell" aria-hidden="true"></i> 
                            </span>
                        </button>
                        </div>
                        <div style="display: none!important;" class="dropdown-menu" id="notifications_content" role="menu">
                        <div class="dropdown-content py-0">
                            <div class="list has-overflow-ellipsis" style="width: 340px" id="notifications_list_parrent">
                            <?php foreach ($notifications as $notification):?>
                            <a id="<?=$notification['0']?>" class="list-item">
                                <div class="list-item-content">
                                <div class="list-item-title"><?=$notification['1']?></div>
                                <div class="list-item-description"><?=$notification['2']?></div>
                                </div>

                                <div class="list-item-controls">
                                <button onclick="notification_mark_read('<?=$notification['0']?>')" class="button is-light is-link">
                                    <span class="icon is-small">
                                    <i class="fas fa-check"></i>
                                    </span>
                                </button>
                                </div>
                            </a>
                            <?php endforeach;?>

                            <a class="list-item">
                                <div class="list-item-content">
                                <div class="list-item-title">Email updated</div>
                                <div class="list-item-description">We just updated your default email address for your account</div>
                                </div>

                                <div class="list-item-controls">
                                <button class="button is-light is-link">
                                    <span class="icon is-small">
                                    <i class="fas fa-check"></i>
                                    </span>
                                </button>
                                </div>
                            </a>

                            <a class="list-item" href="https://www.figma.com/community/file/1180350557509236996" target="_blank">
                                <div class="list-item-content">
                                <div class="list-item-title">Get Bulma list for Figma</div>
                                <div class="list-item-description">Use these components in your designs</div>
                                </div>

                                <div class="list-item-controls">
                                <button class="button is-light is-link">
                                    <span class="icon is-small">
                                    <i class="fas fa-arrow-right"></i>
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
                <div class="list has-visible-pointer-controls">
                    <div class="list-item">
                        <div class="list-item-image">
                        <figure class="image is-64x64">
                            <img class="is-rounded" src="https://via.placeholder.com/128x128.png?text=Image">
                        </figure>
                        </div>

                        <div class="list-item-content">
                        <div class="list-item-title">List item</div>
                        <div class="list-item-description">List item description</div>
                        </div>

                        <div class="list-item-controls">
                        <div class="buttons is-right">
                            <button class="button">
                            <span class="icon is-small">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span>Edit</span>
                            </button>

                            <button class="button">
                            <span class="icon is-small">
                                <i class="fas fa-ellipsis-h"></i>
                            </span>
                            </button>
                        </div>
                        </div>
                    </div>

                    <div class="list-item">
                        <div class="list-item-image">
                        <figure class="image is-64x64">
                            <img class="is-rounded" src="https://via.placeholder.com/128x128.png?text=Image">
                        </figure>
                        </div>

                        <div class="list-item-content">
                        <div class="list-item-title">List item</div>
                        <div class="list-item-description">List item description</div>
                        </div>

                        <div class="list-item-controls">
                        <div class="buttons is-right">
                            <button class="button">
                            <span class="icon is-small">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span>Edit</span>
                            </button>

                            <button class="button">
                            <span class="icon is-small">
                                <i class="fas fa-ellipsis-h"></i>
                            </span>
                            </button>
                        </div>
                        </div>
                    </div>

                    <div class="list-item">
                        <div class="list-item-image">
                        <figure class="image is-64x64">
                            <img class="is-rounded" src="https://via.placeholder.com/128x128.png?text=Image">
                        </figure>
                        </div>

                        <div class="list-item-content">
                        <div class="list-item-title">List item</div>
                        <div class="list-item-description">List item description</div>
                        </div>

                        <div class="list-item-controls">
                        <div class="buttons is-right">
                            <button class="button">
                            <span class="icon is-small">
                                <i class="fas fa-edit"></i>
                            </span>
                            <span>Edit</span>
                            </button>

                            <button class="button">
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
    notification_conn.open("GET", "./dashboard/api/remove_notification.php?id="+notification_uuid);
    notification_conn.send();
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
    console.log(this.responseText);
    notifications_temp = '';
    for (var notification_array in notifications_array) {
        if(notifications_array[notification_array][1] == "Access denied") {
            show_notification('error','Your session has expired, please sign in.');
        }
        console.log(notifications_array[notification_array][0] + "title:" + notifications_array[notification_array][1]);
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
    show_notification('syncing','Syncronizing notifications...');
    notification_sync();
}
function show_notification(type,message){
    switch(type){
        case 'syncing':
            bulmaToast.toast({ message: message, position: 'bottom-right', type: 'is-success', opacity: 0.8, duration: 500 });
            break;
        case 'error':
            bulmaToast.toast({ message: message, position: 'bottom-right', type: 'is-warning', opacity: 0.8, duration: 5000 });
            break;
    }
}
</script>