function add_new_server_field_server_type_change() {
    form_submit_button = document.getElementById('add_new_server_button_submit');
    switch (document.getElementById('add_new_server_field_server_type').value) {
        case "default":
            form_submit_button.disabled = true;
            break;
        case "backup_location":
            form_submit_button.disabled = false;
            break;
        case "backup_server":
            form_submit_button.disabled = false;
            break;
        default:
            form_submit_button.innerHTML = "ERROR!";
            break;
    }
}
function add_new_server_field_display_name_change() {
    console.log("add_new_server_field_display_name_change");
    form_field_display_name_icon_right = document.getElementById('add_new_server_field_display_name_icon_right');
    console.log(field_is_valid('add_new_server_field_display_name'));
    switch (field_is_valid('add_new_server_field_display_name')) {
        case true:
            console.log("marking as valid.");
            form_field_display_name_icon_right.classList.add('fa-check');
            form_field_display_name_icon_right.classList.remove('fa-x');
            form_field_display_name_icon_right.classList.remove('form_field_icon_default');
            form_field_display_name_icon_right.classList.remove('form_field_icon_invalid');
            form_field_display_name_icon_right.classList.add('form_field_icon_valid');
            break;
        case false:
            console.log("marking as invalid!");
            form_field_display_name_icon_right.classList.remove('fa-check');
            form_field_display_name_icon_right.classList.add('fa-x');
            form_field_display_name_icon_right.classList.remove('form_field_icon_default');
            form_field_display_name_icon_right.classList.remove('form_field_icon_valid');
            form_field_display_name_icon_right.classList.add('form_field_icon_invalid');
            break;
        default:
            console.log("marking as default!");
            form_field_display_name_icon_right.classList.add('fa-check');
            form_field_display_name_icon_right.classList.remove('fa-x');
            break;
    }
}
// Validate field value
function field_is_valid(field_id) {
    console.log("Running field validator");
    field = document.getElementById(field_id);
    if (field.value.length >= 1) {
        console.log("field was valid");
        return true;
    } else {
        console.log("field was invalid!");
        return false;
    }
}

//<!-- Script to activate/toggle visibility of the create a new server Modal -->
function show_modal(modal_id) {
    document.getElementById(modal_id).classList.add('is-active');
    if (modal_id == "create-new-server") {
        add_new_server_field_server_type_change();
    }
}