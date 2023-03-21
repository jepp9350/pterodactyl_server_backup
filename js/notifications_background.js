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
    notification_conn.open("GET", "./index.php?api_key=none&action=remove_notification&notification_id="+notification_uuid);
    notification_conn.send();
}
// Javascript if the user clicks outside the notifications drop down
window.onclick = function(event) {
        if (event.target.closest('#notifications') == null) {
            //log the event target parent element to see what it is
            //console.log('is null');
            document.getElementById("notifications_content").style.display="none";
        }
}