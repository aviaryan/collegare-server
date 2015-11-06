import requests
import hashlib

def createaccount(payload):
	'''
	Creates the Collegare account
	'''
	payload['phash'] = hashlib.sha256( str.encode( payload['phash'] ) ).hexdigest()
	r = requests.post(server + "/makeaccount.php", data=payload)
	if r.ok:
		print(r.content)



server = "http://collegare.eu5.org"
server = "http://localhost/collegare-server"

payload = {
	    'firstname': 'test',
	    'lastname': 'ac100',
	    'username': 'test100',
	    'phash': 'qwerty'
	}

createaccount( payload )