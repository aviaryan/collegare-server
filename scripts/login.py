import requests
import hashlib
import json

server = "http://collegare.eu5.org"
server = "http://localhost/collegare-server"

username="test2"
password="qwerty"

phash = hashlib.sha256( str.encode(password) ).hexdigest()
# print( phash )

payload = { 'username': username , 'password': phash }

r = requests.post(server + "/login.php", data = payload)

if r.ok:
	json_res = json.loads( bytes.decode(r.content) )
	print( json.dumps(json_res, indent=4, sort_keys=True) )

# log 2
payload = { 'id': json_res['id'], 'token': json_res['token'] }
r = requests.post(server + "/login.php", data = payload)
if r.ok:
	json_res = json.loads( bytes.decode(r.content) )
	print( json.dumps(json_res, indent=4, sort_keys=True) )