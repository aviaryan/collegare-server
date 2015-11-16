import requests
import hashlib
import json
import random

# the script creates tables, then users, posts, comments etc

server = "http://collegare.eu5.org"
server = "http://localhost/collegare-server"
password = "qwerty"
passhash = hashlib.sha256( str.encode( password ) ).hexdigest()
step = 0

# ---------------------------------------

def createaccount(payload):
	'''
	Creates the Collegare account
	'''
	payload['phash'] = hashlib.sha256( str.encode( payload['phash'] ) ).hexdigest()
	r = requests.post(server + "/makeaccount.php", data=payload)
	if r.ok:
		print(r.content)


def getauth(username, passhash):
	'''
	Gets the authentication token of an account
	'''
	payload = { 'username': username, 'password': passhash }
	r = requests.post(server + "/login.php", data = payload)
	json_res = json.loads( bytes.decode(r.content) )
	return json_res['token']


# ---------------------------------------

print('creating tables....')

if step<1:
	arr = ['dropall', 'eyeds', 'tgroups', 'tgnetwork', 'cposts', 'cmnts', 'vts', 'msgs', 'auths']
	for i in arr:
		r = requests.get(server + '/builders/' + i + '.php')
		print(r.content)

print('creating tables done')
print('creating users')

if step<2:
	for i in range(1,6):
		firstname = 'test';
		lastname = 'ac' + str(i);
		username = 'test' + str(i);
		payload = {
		    'firstname': firstname,
		    'lastname': lastname,
		    'username': username,
		    'phash': password
		}
		createaccount( payload )

print('users done')
print('creating posts')

if step<3:
	for i in range(1,10):
		tid = random.randrange(5)+1
		token = getauth('test' + str(tid), passhash)
		payload = { 'action': 'set', 'id': tid , 'content': 'alpha ' + str(random.randrange(0,101)), 'token': token };
		r = requests.post(server + "/post.php", data=payload)
		if r.ok:
			print( r.content )

print('posts done')
print('creating comments')

if step<4:
	for i in range(1,15):
		tid = random.randrange(5)+1
		token = getauth('test' + str(tid), passhash)
		tpostid = random.randrange(8)+1
		payload = { 'action': 'comment', 'id': tid , 'content': 'comment ' + str(random.randrange(50,150)), 'token': token, 'postid': tpostid };
		r = requests.post(server + "/post.php", data=payload)
		if r.ok:
			print( r.content )

print('comments done')
print('now voting')

vs = ['upvote', 'downvote', 'none']
if step<5:
	for i in range(1,15):
		tid = random.randrange(5)+1
		token = getauth('test' + str(tid), passhash)
		tpostid = random.randrange(8)+1
		action = vs[ (tid+tpostid)%3 ]
		payload = { 'action': action, 'id': tid, 'token': token, 'postid': tpostid };
		r = requests.post(server + "/vote.php", data=payload)
		print( r.content )