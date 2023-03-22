function run_backup(backup_plan_id) {
    // Javascript => Backend => run_backup

    // Create an XMLHttpRequest object
    const run_backup_job = new XMLHttpRequest();
    run_backup_job.onload = function() {
        // Here you can use the Data
        show_notification("success", "Backup job started...");
        if (this.responseText == "200") {
            show_notification("success", "Backup job was successful");
        } else {
            show_notification("error", "Backup job failed");
        }
    }
    run_backup_job.onerror = function() {
        // Here you can use the Data
        show_notification("error", "Backup job failed");
    }
    run_backup_job.onprogress = function() {
        // Here you can use the Data
        show_notification("info", "Backup job in progress...");
    }
    run_backup_job.onabort = function() {
        // Here you can use the Data
        show_notification("warning", "Backup job aborted");
    }
    run_backup_job.ontimeout = function() {
        // Here you can use the Data
        show_notification("warning", "Backup job timed out");
    }

    // Send a request to the server
    run_backup_job.open("GET", "./index.php?api_key=none&action=run_backup_job&backup_plan_id="+backup_plan_id);
    run_backup_job.timeout = 12000; // time in milliseconds
    run_backup_job.send();
}