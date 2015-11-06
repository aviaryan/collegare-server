import requests
import hashlib
import json

server = "http://collegare.eu5.org"
server = "http://localhost/collegare-server"

username="test2"
password="qwerty"

phash = hashlib.sha256( str.encode(password) ).hexdigest()
# print( phash )

dopost = 0
payload = { 'username': username , 'password': phash }
r = requests.post(server + "/login.php", data = payload)

if r.ok and dopost:
	json_res = json.loads( bytes.decode(r.content) )
	token = json_res['token']

	payload = { 'do': 'set', 'id': json_res['id'] , 'content': 'tupid not post', 'groupid': 2, 'token': json_res['token'] };
	r = requests.post(server + "/post.php", data=payload)
	if r.ok:
		print( r.content )

# assuming that the post was created

getpost = 1
if getpost:
	payload = { 'do': 'feed', 'id': 1 };
	r = requests.post(server + "/post.php", data = payload)
	if r.ok:
		print( json.dumps( json.loads(bytes.decode(r.content)) , indent=4 ) )