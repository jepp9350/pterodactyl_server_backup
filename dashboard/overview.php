<div class="container">
    <div class="block">
        <div class="columns mt-4">
            <div class="column">
                <div class="box">
                <!-- Notifications start-->
                <div id="notifications">
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
      <!-- Service displayname field -->
      <div class="field">
        <label class="label">Server nickname (displayname)</label>
        <p class="control has-icons-left has-icons-right">
            <input oninput="add_new_server_field_display_name_change()" id="add_new_server_field_display_name" class="input" type="text" placeholder="My server 1 (name)">
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
    </section>
    <footer class="modal-card-foot">
      <button id="add_new_server_button_submit" class="button is-success" disabled>Submit</button>
      <button class="button">Cancel</button>
      <button class="button is-warning">Reset fields</button>
    </footer>
  </div>
</div>
<!-- Insert javascript -->
<!-- Create a new server => Modal => JS -->
<script src="./js/bulma.modal.js"></script>
<!-- Create a new server => validate form => JS -->
<script src="./js/add_new_server_form.js"></script>
<!-- Dashboard => Background => Syncronize => JS -->
<script src="./js/dashboard_background_sync.js"></script>