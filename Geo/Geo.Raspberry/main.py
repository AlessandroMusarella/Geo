import time
import requests
from w1thermsensor import W1ThermSensor
sensor = W1ThermSensor()

CLIENTID = 1
domain = "toshiba.tripi.eu"


def sendServer(clientID, temperature, light, humidity):
	r = requests.get("https://"+domain+"/geo/geoApi.php", {"method": "upload", "clientID": clientID, "temperature": temperature, "light": light, "humidity": humidity})
	print(r.status_code)
	print(r.text)
	return r.status_code == 200


while True:
	temp = sensor.get_temperature()
	print("The temperature is %s celsius" % temp)
	sendServer(CLIENTID, temp, 0, 0)
	time.sleep(60)
