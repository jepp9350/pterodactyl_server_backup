import requests, json, psutil

diskinfolist = []

def bytes_to_GB(bytes):
    gb = bytes/(1024*1024*1024)
    gb = round(gb, 2)
    return gb

# get the secret token from the file
with open('secret_token') as f:
    secret_token = f.readline(48)

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
        "diskMountpoint": partition.device,
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
response = requests.post('http://172.16.13.33/', data=data)

# if no action = die
if (response.text == 'no action'):
    print('no action')
    exit()

# there is an action pending, json decode it
# action = json.loads(response.text)
print (response.text)

# task = action[0]
# taskdir = action[1]
