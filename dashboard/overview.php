<div class="container">
    <div class="block">
        <div class="columns mt-4">
            <div class="column">
                <div class="box">
                    <div class="columns">
                        <div class="column is-half">
                            <p class="is-size-4">Overview</p>
                            <p class="is-size-6">Backup servers</p>
                        </div>
                        <div class="column is-half">
                        <!-- Notifications start-->
                            <div id="notifications">
                                <div class="is-flex is-justify-content-end">
                                <div class="dropdown is-active is-right">
                                    <div class="dropdown-trigger">
                                    <button onclick="toggle_notifications()" id="notifications_button" class="button is-focused" aria-haspopup="true" aria-controls="dropdown-menu">
                                        <span id="notificatitons_span" class="icon is-small"><span style="display: none;" title="Notifications" id="notifications_count" class="badge">0</span>
                                        <i id="notifications_icon" class="fas fa-bell" aria-hidden="true"></i> 
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
                    </div>
                </div>
                <!-- Backup server list start -->
                <div id="backup_servers_list_parrent_overview" class="list has-visible-pointer-controls">
                    <div class="list-item">
                        <div class="list-item-image">
                            <figure class="image is-64x64">
                                <img src="https://via.placeholder.com/128x128.png?text=Image" class="is-rounded">
                            </figure>
                        </div>
                        <div class="list-item-content">
                        <div class="list-item-title">Loading backup servers...</div>
                        <div class="list-item-description">Please wait while your backup servers are loading.</div>
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
<!-- Create a new server => Modal -->
<div id="create-new-server" class="modal">
  <div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">Add a new server</p>
      <button class="delete" aria-label="close"></button>
    </header>
    <section class="modal-card-body">
      <!-- Content ... -->
        <!-- Steps -->
        <ul class="steps has-content-centered">
            <li id="form_step_1" class="steps-segment is-dashed is-active">
                <span class="steps-marker"></span>
                <div class="steps-content">
                    <p class="is-size-4">Step 1</p>
                    <p>Choose server type</p>
                </div>
            </li>
            <li id="form_step_2" class="steps-segment is-dashed">
                <span class="steps-marker"></span>
                <div class="steps-content">
                    <p class="is-size-4">Step 2</p>
                    <p>Configure settings.</p>
                </div>
            </li>
            <li id="form_step_3" class="steps-segment is-dashed">
                <span class="steps-marker"></span>
                <div class="steps-content">
                    <p class="is-size-4">Step 3</p>
                    <p>Install server.</p>
                </div>
            </li>
        </ul>
        <!-- End of Steps -->
      <!-- Service displayname field -->
      <div class="field">
        <label class="label">Server nickname (displayname)</label>
        <p class="control has-icons-left has-icons-right">
            <input name="server_displayname" oninput="add_new_server_field_display_name_change()" id="add_new_server_field_display_name" class="input" type="text" placeholder="My server 1 (name)">
            <span class="icon is-small is-left">
                <i class="fa-solid fa-server"></i>
            </span>
            <span class="icon is-small is-right">
                <i id="add_new_server_field_display_name_icon_right" class="fas fa-check"></i>
            </span>
        </p>
      </div>
        <!-- Service type field -->
      <div class="field">
        <label class="label">Choose server type</label>
        <p class="control has-icons-left">
            <span class="select">
            <select onchange="add_new_server_field_server_type_change()" id="add_new_server_field_server_type" name="server_type">
                <option selected value="default">Server type</option>
                <option value="backup_location">Store backups on this server (backup location)</option>
                <option value="backup_server">Backup this server</option>
            </select>
            </span>
            <span class="icon is-small is-left">
            <i class="fa-solid fa-gear"></i>
            </span>
        </p>
      </div>
    <!-- Backup server => settings -->
      <div id="add_new_server_settings_backup_server" style="display:none;">
        <div class="divider">Settings</div>
        <label class="label">What backup server should this server use?</label>
        <div id="notification_no_storage_servers_notifications" class="notification is-warning">
            <strong>Warning! You don't have any backup locations configured.</strong><br>
            There are no backup locations configured. You can't backup any servers until you configure at least one backup location.
        </div>
        <p class="control has-icons-left">
            <span class="select">
            <select onchange="add_new_server_field_server_location_change()" id="add_new_server_field_server_location" name="server_location">
                <option selected value="default">Backup location</option>
            </select>
            </span>
            <span class="icon is-small is-left">
                <i class="fa-solid fa-location-dot"></i>
            </span>
        </p>
      </div>
      <!-- Backup location => settings -->
      <div id="add_new_server_settings_backup_location" style="display:none;">
        <div class="divider">Settings</div>
        <div class="field">
            <label class="label">What location should backups be saved in?</label>
            <p class="control has-icons-left">
            <span class="select">
                <select onchange="add_new_server_field_server_backup_location_change()" id="add_new_server_field_server_backup_location" name="server_location">
                    <option selected value="default">Choose server directory</option>
                    <option value="backups">/backups</option>
                    <option value="home_backups">/home/backups</option>
                    <option value="custom">Other (custom)</option>
                </select>
            </span>
            <span class="icon is-small is-left">
                <i class="fa-solid fa-location-dot"></i>
            </span>
            </p>
        </div>
        <div id="add_new_server_settings_backup_location_custom" style="display:none;">
            <div class="field">
                <label class="label">Custom directory</label>
                <p class="control has-icons-left has-icons-right">
                    <input name="server_backup_location_custom" oninput="add_new_server_field_server_backup_location_custom_change()" id="add_new_server_field_server_backup_location_custom" class="input" type="text" placeholder="Custom directory">
                    <span class="icon is-small is-left">
                        <i class="fa-solid fa-folder"></i>
                    </span>
                    <span class="icon is-small is-right">
                        <i id="add_new_server_field_server_backup_location_custom_icon_right" class="fas fa-check"></i>
                    </span>
                </p>
            </div>
            <div style="display: none;" id="add_new_server_settings_backup_location_custom_notification" class="notification is-success is-light">
                <strong>Location format.</strong><br>
                Enter the location of the directory where you want to store backups. For example: /home/backups
            </div>
        </div>
        <div class="columns is-multiline">
            <div class="column is-half">
                <!-- SSH credentials => Hostname -->
                <div class="field">
                    <label class="label">SSH hostname</label>
                    <p class="control has-icons-left has-icons-right">
                        <input name="server_ssh_hostname" oninput="add_new_server_field_server_ssh_hostname_change()" id="add_new_server_field_server_ssh_hostname" class="input" type="text" placeholder="www.example.com">
                        <span class="icon is-small is-left">
                            <i class="fa-solid fa-server"></i>
                    </p>
                </div>
                <!-- SSH credentials => Username -->
                <div class="field">
                    <label class="label">SSH username</label>
                    <p class="control has-icons-left has-icons-right">
                        <input name="server_ssh_username" oninput="add_new_server_field_server_ssh_username_change()" id="add_new_server_field_server_ssh_username" class="input" type="text" placeholder="backupuser">
                        <span class="icon is-small is-left">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span class="icon is-small is-right">
                            <i id="add_new_server_field_server_ssh_username_icon_right" class="fas fa-check"></i>
                        </span>
                    </p>
                </div>
            </div>
            <div class="column is-half">
                <!-- SSH credentials => Password -->
                <div class="field">
                    <label class="label">SSH password</label>
                    <p class="control has-icons-left has-icons-right">
                        <input name="server_ssh_password" oninput="add_new_server_field_server_ssh_password_change()" id="add_new_server_field_server_ssh_password" class="input" type="password" placeholder="********">
                        <span class="icon is-small is-left">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <span class="icon is-small is-right">
                            <i id="add_new_server_field_server_ssh_password_icon_right" class="fas fa-check"></i>
                        </span>
                    </p>
                </div>
            </div>
            <div class="column is-half">
                <!-- SSH credentials => Port -->
                <div class="field">
                    <label class="label">SSH port</label>
                    <p class="control has-icons-left has-icons-right">
                        <input name="server_ssh_port" oninput="add_new_server_field_server_ssh_port_change()" id="add_new_server_field_server_ssh_port" class="input" type="text" placeholder="22">
                        <span class="icon is-small is-left">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <span class="icon is-small is-right">
                            <i id="add_new_server_field_server_ssh_port_icon_right" class="fas fa-check"></i>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    </section>
    <footer class="modal-card-foot">
      <button onclick="create_server_button()" name="submit" id="add_new_server_button_submit" class="button is-success" disabled>Submit</button>
      <button class="button">Cancel</button>
      <button onclick="reset_fields()" class="button is-warning">Reset fields</button>
    </footer>
  </div>
</div>
<!-- Manage server modal -->
<div class="modal" id="server_overview_manage">
<div class="modal-background"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title">Managing a backup server</p>
      <button class="delete" aria-label="close"></button>
    </header>
    <section id="server_overview_manage_body" class="modal-card-body">
        <!-- Content here -->
                <div class="title is-4" id="manage_backup_server_title">none selected</div>
                <div class="subtitle is-6" id="manage_backup_server_subtitle">ID: '+backup_servers_array[backup_server_array][0]+' IP: '+backup_servers_array[backup_server_array][3]+' Last seen: '+timeSince(backup_servers_array[backup_server_array][4])+' Reg: '+backup_servers_array[backup_server_array][5]+'</div>
                <div class="columns is-multiline control_server_dashboard" style="display:'+is_displayed+';" id="server_manage_id_'+backup_servers_array[backup_server_array][0]+'">
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
                    <div class="columns">
                        <div class="column">
                            <p class="is-6">Backup storage: 60%</p>
                            <progress class="progress is-success" value="60" max="100">60%</progress>
                        </div>
                        <div class="column">
                            <p class="is-6">Total storage: 60%</p>
                            <progress class="progress is-warning" value="60" max="100">60%</progress>
                        </div>
                    </div>
                    <!-- End of server storage -->
                </div>
                <div class="column is-12">
                    <div class="divider">Server information</div>
                    <!-- Server information -->
                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label">Server name</label>
                                <div class="control">
                                    <input class="input" type="text" placeholder="Server name">
                                </div>
                                <p class="help">This is the name of the server.</p>
                            </div>
                        </div>
                    </div>
                    <!-- End of server information -->
                    </div>
                </div>
    </section>
    <footer class="modal-card-foot">
      <button class="button is-success">Save changes</button>
      <button class="button">Cancel</button>
    </footer>
</div>
<!-- Insert javascript -->
<!-- Create a new server => Modal => JS -->
<script src="./js/bulma.modal.js"></script>
<!-- Create a new server => validate form => JS -->
<script src="./js/add_new_server_form.js"></script>
<!-- Dashboard => Background => Syncronize => JS -->
<script src="./js/dashboard_background_sync.js"></script>
<!-- Dashboard => Background => Notifications => JS -->
<script src="./js/notifications_background.js"></script>