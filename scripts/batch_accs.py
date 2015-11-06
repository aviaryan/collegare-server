import requests
import hashlib

server = "http://collegare.eu5.org"
# server = "http://localhost/collegare-server"


def createaccount(payload):
	'''
	Creates the Collegare account
	'''
	payload['phash'] = hashlib.sha256( str.encode( payload['phash'] ) ).hexdigest()
	r = requests.post(server + "/makeaccount.php", data=payload)
	if r.ok:
		print(r.content)


for i in range(1,10):
	firstname = 'test';
	lastname = 'ac' + str(i);
	username = 'test' + str(i);
	phash = 'qwerty';

	payload = {
	    'firstname': firstname,
	    'lastname': lastname,
	    'username': username,
	    'phash': phash
	}

	createaccount( payload )