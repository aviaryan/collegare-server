import requests
import hashlib
import json

server = "http://collegare.eu5.org"
server = "http://localhost/collegare-server"

username="test3"
password="qwerty"

phash = hashlib.sha256( str.encode(password) ).hexdigest()
# print( phash )

payload = { 'username': username , 'password': phash }
r = requests.post(server + "/login.php", data = payload)
if r.ok:
	json_res = json.loads( bytes.decode(r.content) )
	print( json.dumps(json_res, indent=4, sort_keys=True) )
uid = json_res['id']


# log 2
payload = { 'id': json_res['id'], 'token': json_res['token'] }
r = requests.post(server + "/login.php", data = payload)
if r.ok:
	json_res = json.loads( bytes.decode(r.content) )
	print( json.dumps(json_res, indent=4, sort_keys=True) )

token = json_res['token']

# get user info
payload = { 'username': json_res['username'], 'action': 'get' }
r = requests.post(server + "/user.php", data = payload)
if r.ok:
	json_res = json.loads( bytes.decode(r.content) )
	print( json.dumps(json_res, indent=4, sort_keys=True) )

# set user info
payload = { 'action': 'set', 'id': uid, 'token': token, 'username': username }
r = requests.post(server + "/user.php", data = payload)
if r.ok:
	json_res = json.loads( bytes.decode(r.content) )
	print( json.dumps(json_res, indent=4, sort_keys=True) )

# set user image
payload = { 'action': 'setpic', 'id': uid, 'token': token }
r = requests.post(server + "/user.php", data = payload, files = {'file': open('profile.png', 'rb')} )
if r.ok:
	print(r.content)
	json_res = json.loads( bytes.decode(r.content) )
	print( json.dumps(json_res, indent=4, sort_keys=True) )