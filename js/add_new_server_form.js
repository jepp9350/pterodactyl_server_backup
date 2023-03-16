// Check if "step 1" fields are valid.
function check_step_1() {
    form_submit_button = document.getElementById('add_new_server_button_submit');
    settings_category_backup_server = document.getElementById('add_new_server_settings_backup_server');
    settings_category_backup_location = document.getElementById('add_new_server_settings_backup_location');
    server_type = document.getElementById('add_new_server_field_server_type').value;
    form_step_1 = document.getElementById('form_step_1');
    form_step_2 = document.getElementById('form_step_2');
    form_step_3 = document.getElementById('form_step_2');
    // Check display_name field
    if (field_is_valid('add_new_server_field_display_name','server_display_name') && server_type != "default") {
        if (server_type == "backup_server") {
            settings_category_backup_server.style.display = 'block';
            settings_category_backup_location.style.display = 'none';
        }
        if (server_type == "backup_location") {
            settings_category_backup_location.style.display = 'block';
            settings_category_backup_server.style.display = 'none';
        }
            form_step_2.classList.add('is-active');
            form_step_1.classList.remove('is-dashed');
            form_step_1.classList.remove('is-active');
    } else {
        form_submit_button.disabled = true;
        settings_category_backup_server.style.display = 'none';
        settings_category_backup_location.style.display = 'none';
        form_step_2.classList.remove('is-active');
        form_step_1.classList.add('is-dashed');
        form_step_1.classList.add('is-active');
    }
    // Check server_type field
    form_field_display_name_icon_right = document.getElementById('add_new_server_field_display_name_icon_right');
    switch (field_is_valid('add_new_server_field_display_name','server_display_name')) {
        case true:
            //console.log("marking as valid.");
            form_field_display_name_icon_right.classList.add('fa-check');
            form_field_display_name_icon_right.classList.remove('fa-x');
            form_field_display_name_icon_right.classList.remove('form_field_icon_default');
            form_field_display_name_icon_right.classList.remove('form_field_icon_invalid');
            form_field_display_name_icon_right.classList.add('form_field_icon_valid');
            return true;
            break;
        case false:
            //console.log("marking as invalid!");
            form_field_display_name_icon_right.classList.remove('fa-check');
            form_field_display_name_icon_right.classList.add('fa-x');
            form_field_display_name_icon_right.classList.remove('form_field_icon_default');
            form_field_display_name_icon_right.classList.remove('form_field_icon_valid');
            form_field_display_name_icon_right.classList.add('form_field_icon_invalid');
            return false;
            break;
        default:
            //console.log("marking as default!");
            form_field_display_name_icon_right.classList.add('fa-check');
            form_field_display_name_icon_right.classList.remove('fa-x');
            return false;
            break;
    }
}
// Check if "step 2" fields are valid.
function check_step_2() {
    form_submit_button = document.getElementById('add_new_server_button_submit');
    server_type = document.getElementById('add_new_server_field_server_type').value;
    form_step_2 = document.getElementById('form_step_2');
    form_step_3 = document.getElementById('form_step_3');
    custom_backup_location = document.getElementById('add_new_server_field_server_backup_location_custom').value;
    backup_server = document.getElementById('add_new_server_field_server_location').value;
    storage_location = document.getElementById('add_new_server_field_server_backup_location').value;
    custom_backup_location_field = document.getElementById('add_new_server_settings_backup_location_custom');
    custom_backup_location_notification = document.getElementById('add_new_server_settings_backup_location_custom_notification');
    // Hide the custom backup location field
    custom_backup_location_field.style.display = 'none';
    custom_backup_location_notification.style.display = 'none';
    document.getElementById('notification_no_storage_servers_notifications').style.display = 'none';
    // Check if the server type is a backup server
    if (server_type == "backup_location") {
        // Check what storage location is selected
        if (storage_location == "custom") {
            if (document.querySelector("#add_new_server_field_server_backup_location").length == 1) {
                document.getElementById('notification_no_storage_servers_notifications').style.display = 'block';
            }
            custom_backup_location_notification.style.display = 'block';
            custom_backup_location_field.style.display = 'block';
            if (custom_backup_location == "" || !field_is_valid('add_new_server_field_server_backup_location_custom','backup_location_custom')) {
                form_submit_button.disabled = true;
                form_step_3.classList.remove('is-active');
                form_step_2.classList.add('is-dashed');
                form_step_2.classList.add('is-active');
                return false;
            }
        }
        if (field_is_valid('add_new_server_field_server_ssh_username','server_display_name') && field_is_valid('add_new_server_field_server_ssh_password','server_display_name') && field_is_valid('add_new_server_field_server_ssh_port','server_display_name')){
            form_submit_button.disabled = false;
            form_step_3.classList.add('is-active');
            form_step_2.classList.remove('is-dashed');
            form_step_2.classList.remove('is-active');
            return true;
        } else {
            form_submit_button.disabled = true;
            form_step_3.classList.remove('is-active');
            form_step_2.classList.add('is-dashed');
            form_step_2.classList.add('is-active');
            return false;
        }
    } else if (server_type == "backup_server") {
        if (backup_server != "" && field_is_valid('add_new_server_field_server_location','backup_server')) {
            form_submit_button.disabled = false;
            form_step_3.classList.add('is-active');
            form_step_2.classList.remove('is-dashed');
            form_step_2.classList.remove('is-active');
            return true;
        } else {
            form_submit_button.disabled = true;
            form_step_3.classList.remove('is-active');
            form_step_2.classList.add('is-dashed');
            form_step_2.classList.add('is-active');
            return false;
        }
    }
 else {
        form_submit_button.disabled = true;
        form_step_3.classList.remove('is-active');
        form_step_2.classList.add('is-dashed');
        form_step_2.classList.add('is-active');
        return false;
    }
}



function add_new_server_field_server_backup_location_change() {
    check_step_2();
}
function add_new_server_field_server_backup_location_custom_change() {
    check_step_2();
}
function add_new_server_field_server_location_change() {
    check_step_2();
}
function add_new_server_field_server_type_change() {
    check_step_1();
}
function add_new_server_field_display_name_change() {
    check_step_1();
}
function add_new_server_field_server_ssh_username_change() {
    check_step_2();
}
function add_new_server_field_server_ssh_password_change() {
    check_step_2();
}
function add_new_server_field_server_ssh_port_change() {
    check_step_2();
}
// Validate field value
function field_is_valid(field_id, requirement_category) {
    // Save the field as a variable.
    field = document.getElementById(field_id);
    // Special characters test:
    var format_special_chars = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
    field_has_special_chars = format_special_chars.test(field.value);
    // Set field requirements (JS)
    switch (requirement_category) {
        case "server_display_name":
            min_length = 1;
            max_length = 32;
            is_directory = false;
            break;
        case "backup_location_custom":
            console.log("backup_location_custom");
            min_length = 1;
            max_length = 32;
            is_directory = true;
            break;
        case "backup_server":
            min_length = 1;
            max_length = 32;
            is_directory = false;
            break;
        default:
            min_length = 1;
            max_length = 32;
            is_directory = false;
            break;
    }
    // Check the category of the field
    if (is_directory || requirement_category == "backup_location_custom") {
        // Check if the field is a directory
        console.log("is_directory");
        var format_directory = /^[a-zA-Z0-9_\/\-]+$/;
        field_is_directory = format_directory.test(field.value);
        if (field_is_directory && field.value.length >= min_length && field.value.length <= max_length) {
            //console.log("field was valid");
            return true;
        }
    } else if (requirement_category == "server_display_name") {
        // Check if the display name is valid
        if (field.value.length >= min_length && field.value.length <= max_length && !field_has_special_chars) {
            //console.log("field was valid");
            return true;
        } else {
            //console.log("field was invalid!");
            return false;
        }
    } else if (requirement_category == "backup_server") {
        // Check if the backup server is valid
        if (field.value != "default" && field.value.length >= min_length && field.value.length <= max_length && !field_has_special_chars) {
            //console.log("field was valid");
            return true;
        } else {
            //console.log("field was invalid!");
            return false;
        }
    } else {
        // category does not exist
        console.log("category does not exist");
        return false;
}
}

//<!-- Script to activate/toggle visibility of the create a new server Modal -->
function show_modal(modal_id) {
    document.getElementById(modal_id).classList.add('is-active');
    if (modal_id == "create-new-server") {
        if (check_step_1()) {
            check_step_2();
        }

    }
}
// Javascript to create a new service/server
function create_server_button() {
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
            }
            server_backup_location = document.getElementById('add_new_server_field_server_backup_location').value;
            server_ssh_username = document.getElementById('add_new_server_field_server_ssh_username').value;
            server_ssh_password = document.getElementById('add_new_server_field_server_ssh_password').value;
            server_ssh_port = document.getElementById('add_new_server_field_server_ssh_port').value;
            break;

    }
    // Create an XMLHttpRequest object
    const notification_conn = new XMLHttpRequest();
    // Define a callback function
    notification_conn.onload = function() {
        // Here you can use the Data
        console.log(this.responseText);
    }
    // Send a request
    // $type,$displayname,$ssh_username,$ssh_password,$ssh_port,$backup_path
    notification_conn.open("GET", "./index.php?api_key=none&action=add_new_server&display_name="+display_name+"&server_type="+server_type+"&server_ssh_username="+server_ssh_username+"&server_ssh_password="+server_ssh_password+"&server_ssh_port="+server_ssh_port+"&server_backup_location="+server_backup_location);
    notification_conn.send();
}
function reset_fields() {
    // Set variables
    display_name = document.getElementById('add_new_server_field_display_name');
    server_type = document.getElementById('add_new_server_field_server_type');
    settings_category_backup_server = document.getElementById('add_new_server_settings_backup_server');
    settings_category_backup_location = document.getElementById('add_new_server_settings_backup_location');
    // Reset fields
    display_name.value = '';
    server_type.value = 'default';
    settings_category_backup_server.style.display = 'none';
    settings_category_backup_location.style.display = 'none';
    // Trigger field update
    add_new_server_field_display_name_change();
    add_new_server_field_server_type_change();
}
