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
json_res = json.loads( bytes.decode(r.content) )

dopost = 0
if r.ok and dopost:
	payload = { 'action': 'set', 'id': json_res['id'] , 'content': 'tupid not post', 'token': json_res['token'] };
	r = requests.post(server + "/post.php", data=payload)
	if r.ok:
		print( r.content )

# assuming that the post was created

getpost = 0
if getpost:
	payload = { 'action': 'feed', 'id': 1 };
	r = requests.post(server + "/post.php", data = payload)
	if r.ok:
		print( json.dumps( json.loads(bytes.decode(r.content)) , indent=4 ) )


getpost = 1
if getpost:
	payload = { 'action': 'get', 'postid': 1 };
	r = requests.post(server + "/post.php", data = payload)
	if r.ok:
		print( json.dumps( json.loads(bytes.decode(r.content)) , indent=4 ) )

dopost = 0
if dopost:
	payload = { 'action': 'comment', 'id': json_res['id'] , 'content': 'just another comment', 'token': json_res['token'], 'postid': 1 };
	r = requests.post(server + "/post.php", data=payload)
	if r.ok:
		print( r.content )