import pymysql

connection = pymysql.connect(host='localhost',
							 user='geo',
							 password='',
							 db='geo',
							 charset='utf8mb4',
							 cursorclass=pymysql.cursors.DictCursor)
cursor = connection.cursor()


cursor.execute("SELECT AVG(TEMPERATURE) AS TEMPERATURE, AVG(LIGHT) AS LIGHT, AVG(HUMIDITY) AS HUMIDITY, PLANT_ID, TYPE FROM data JOIN plants ON data.PLANT_ID = plants.ID WHERE UPLOAD_DATE >= DATE_SUB(NOW(), INTERVAL 1 HOUR) GROUP BY PLANT_ID, TYPE")
data = cursor.fetchall()
for row in data:
	temperature = row["TEMPERATURE"]
	light = row["LIGHT"]
	humidity = row["HUMIDITY"]
	plantID = row["PLANT_ID"]
	type = row["TYPE"]
	point = 1.1
	cursor.execute("INSERT INTO points(PLANT_ID, ADD_POINTS) VALUES(" + str(plantID) + ", " + str(point) + ")")
	#print("INSERT INTO points(PLANT_ID, ADD_POINTS) VALUES(" + str(plantID) + ", " + str(point) + ")")
	#print(row)

connection.commit()
