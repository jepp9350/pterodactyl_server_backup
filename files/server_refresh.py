import os, requests, json, psutil
from datetime import datetime

def bytes_to_GB(bytes):
    gb = bytes/(1024*1024*1024)
    gb = round(gb, 2)
    return gb

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
    diskinfolist.append(diskinfo)
    
diskinfolist = json.dumps(diskinfolist)

data = {
    'action': 'u',
    'secret_token': secret_token,
    'diskinfo': diskinfolist,
}
response = requests.post(install_url, data=data)

# if no action = die
if (response.text == 'no action'):
    print('no action')
    exit()

# there is an action pending, json decode it
# action = json.loads(response.text)
# task = action[0]
# taskdir = action[1]
print (response.text)

# now = datetime.now()
# dt_string = now.strftime("%d-%m-%Y.%H-%M-%S")

# dir = '/etc/pterodactyl-backup-service/test'
# cmd = 'tar -C ' + dir + ' -czf tmp/file-backup_' + dt_string + ".tar.gz ./"
# print (cmd)
# stream = os.popen(cmd)
# output = stream.read()
# print (output)


