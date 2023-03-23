import subprocess, os, requests, json, psutil
from datetime import datetime

#   vars in action task
# backupType = 'files'
# sshBackupDir
# backupDir = '/var/lib/pterodactyl/daemon-data/' # vigtigt med sidste '/'
# sshUser
# sshPassword
# sshAdress
# sshPort
# sshBackupDir
# backup_plan_id
# service_id
# backup_server_id


def filesBackup():
    # get the date for the backup name
    now = datetime.now()
    backupTimestamp = now.strftime("%d-%m-%Y.%H-%M-%S") # for backup
    date_and_time = now.strftime("%Y-%m-%d-%H-%M-%S") # for database

    # the shell tar command to take the backup and temporarily store it in the tmp dir
    cmd = 'tar -v -C ' + backupDir + ' -czf tmp/file-backup_' + backupTimestamp + '.tar.gz ./'
    result = subprocess.run([cmd], capture_output=True, text=True, shell=True, check=False)
    if (result.stderr):
        # the tar command failed, print the error msg, and delete the empty tar.gz file
        print(result.stderr)
        backup_status = 'failed'
        updateDatabase(backup_plan_id, service_id, backup_server_id, '', backupType, date_and_time, '', backup_status, result.stderr)
        subprocess.run(['rm tmp/file-backup_' + backupTimestamp + '.tar.gz'], capture_output=True, text=True, shell=True, check=False)
        exit()
    else:
        print(result.stdout)


    # code to upload the backup to the backup server
    cmd = 'sshpass -p "' + sshPassword + '" scp -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -P ' + sshPort + ' tmp/file-backup_' + backupTimestamp + ".tar.gz " + sshUser + '@' + sshAdress + ':' + sshBackupDir
    result = subprocess.run([cmd], capture_output=True, text=True, shell=True, check=False)

    sshWarning = "Warning: Permanently added '[" + sshAdress + "]:" + sshPort + "' (ECDSA) to the list of known hosts.\n"

    if (result.stderr and result.stderr != sshWarning): # check if the error is only the fingerprint warning.
        # the scp command failed, print the error msg
        print(result.stderr)
        files_backup_path = sshBackupDir + 'file-backup_' + backupTimestamp + '.tar.gz'
        backup_file = os.stat('tmp/file-backup_' + backupTimestamp + '.tar.gz')
        backup_size = backup_file.st_size
        backup_status = 'failed'
        updateDatabase(backup_plan_id, service_id, backup_server_id, files_backup_path, backupType, date_and_time, backup_size, backup_status, result.stderr)
        subprocess.run(['rm tmp/file-backup_' + backupTimestamp + '.tar.gz'], capture_output=True, text=True, shell=True, check=False)
        exit()
    else:
        print(result.stdout)


    # the tar.gz has succesfully been created and uploaded, so we update the database with the new backup.

    files_backup_path = sshBackupDir + 'file-backup_' + backupTimestamp + '.tar.gz'
    backup_file = os.stat('tmp/file-backup_' + backupTimestamp + '.tar.gz')
    backup_size = backup_file.st_size
    backup_status = 'succes'

    updateDatabase(backup_plan_id, service_id, backup_server_id, files_backup_path, backupType, date_and_time, backup_size, backup_status, '')

    # the backup is uploaded and the db is updated with the backup info, so we delete the local backup file.
    subprocess.run(['rm tmp/file-backup_' + backupTimestamp + '.tar.gz'], capture_output=True, text=True, shell=True, check=False)


def bytes_to_GB(bytes):
    gb = bytes/(1024*1024*1024)
    gb = round(gb, 2)
    return gb

def updateDatabase(backup_plan_id, service_id, backup_server_id, files_backup_path, backupType, date_and_time, backup_size, backup_status, logOutput):
    backupInfo = [backup_plan_id, service_id, backup_server_id, files_backup_path, backupType, date_and_time, backup_size, backup_status, logOutput[:-1]]
    # print (backupInfo)
    backup_info = json.dumps(backupInfo)
    data = {
    'action': 'uploadBackup',
    'secret_token': secret_token,
    'backup_info': backup_info,
    }
    response = requests.post(install_url, data=data)

    if (response.text == 'succes'):
        print ('successfully updated db')
    else:
        print (response.text)        
    


# get the secret token from the file
with open('secret_token') as f:
    secret_token = f.readline(48)

# get the UI url from the file
with open('install_url') as f:
    install_url = f.read()
    install_url = install_url[:-1]


diskinfolist = []
# get the partitions
disk_partitions = psutil.disk_partitions()
for partition in disk_partitions:
    # exclude some partitions
    if 'boot' in partition.mountpoint or 'squashfs' in partition.fstype:
        continue
    
    disk_usage = psutil.disk_usage(partition.mountpoint)
    diskTotalSpace = str(bytes_to_GB(disk_usage.total))+ "GB"
    diskFreeSpace = str(bytes_to_GB(disk_usage.free))+ "GB"
    diskUsedSpace = str(bytes_to_GB(disk_usage.used)) + "GB"
    discPercUsed = str(disk_usage.percent) + "%"

    diskinfo = {
        "diskPart": partition.device,
        "diskMountpoint": partition.mountpoint,
        "diskTotalSpace": diskTotalSpace,
        "diskFreeSpace": diskFreeSpace,
        "diskUsedSpace": diskUsedSpace,
        "discPercUsed": discPercUsed
    }
    # put the info from each disk in the final list
    diskinfolist.append(diskinfo)
    
# json encode the disk info list
diskinfolist = json.dumps(diskinfolist)

data = {
    'action': 'fetchAction',
    'secret_token': secret_token,
    'diskinfo': diskinfolist,
}
response = requests.post(install_url, data=data)

# if no action = die
if (response.text == 'no action'):
    print('no action')
    exit()


# there is an action pending, json decode it
action = json.loads(response.text)
# [["backupType","backupDir","sshUser","sshPassword","sshAdress","sshPort","sshBackupDir","backup_plan_id","service_id","backup_server_id"]]
# [["files","/etc/pterodactyl-backup-service/test","backupuser","xx","localhost","24","/home/backupuser/23/","0","0","0"]]

if (action[0] == 'files'):
    backupType = action[0]
    backupDir = action[1]
    sshUser = action[2]
    sshPassword = action[3]
    sshAdress = action[4]
    sshPort = action[5]
    sshBackupDir = action[6]
    backup_plan_id = action[7]
    service_id = action[8]
    backup_server_id = action[9]
    
    filesBackup()
else:
    print(action[0], 'is not supported yet')

