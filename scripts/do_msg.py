import requests
import hashlib
import json

server = "http://collegare.eu5.org"
#server = "http://localhost/collegare-server"

username="test3"
password="qwerty"

phash = hashlib.sha256( str.encode(password) ).hexdigest()

payload = { 'username': username , 'password': phash }
r = requests.post(server + "/login.php", data = payload)
print(r.content)
json_res = json.loads( bytes.decode(r.content) )

dopost = 1
if r.ok and dopost:
	payload = { 'action': 'send', 'id': json_res['id'] , 'content': 'Lorem ipsum dolor sit amet.', 'recid': 1, 'token': json_res['token'] };
	r = requests.post(server + "/message.php", data=payload)
	print(r.content)
	if r.ok:
		print( r.content )

# assuming that the post was created

getpost = 1
if getpost:
	payload = { 'action': 'feed', 'id': 3, 'token': json_res['token'] };
	r = requests.post(server + "/message.php", data = payload)
	print(r.content)
	if r.ok:
		print( json.dumps( json.loads(bytes.decode(r.content)) , indent=4 ) )


getchat = 0
if getchat:
	payload = { 'action': 'feedbyuser', 'id': 3, 'recid': 2, 'token': json_res['token'] };
	r = requests.post(server + "/message.php", data = payload)
	print(r.content)
	if r.ok:
		print( json.dumps( json.loads(bytes.decode(r.content)) , indent=4 ) )