<?php

require_once "configuration.php";

$method = $_GET["method"] ?? ltrim(filter_input(INPUT_SERVER, "PATH_INFO"), "/");


if($method === "upload") {
	$plantID = filter_input(INPUT_GET, "plantID");
	$temperature = filter_input(INPUT_GET, "temperature");
	$light = filter_input(INPUT_GET, "light");
	$humidity = filter_input(INPUT_GET, "humidity");
	$sensorsData = new SensorsData($temperature, $light, $humidity);
    uploadData($plantID, $sensorsData);
} elseif ($method === "get") {
	$plantID = filter_input(INPUT_GET, "plantID");
    getData($plantID);
}  elseif ($method === "addPlant") {
	$userID = filter_input(INPUT_GET, "userID");
	$plantName = filter_input(INPUT_GET, "plantName");
	$plantType = filter_input(INPUT_GET, "plantType");
	addPlant($userID, $plantName, $plantType);
} elseif ($method === "getUser") {
	$userID = filter_input(INPUT_GET, "userID");
	getUser($userID);
} elseif ($method === "getPlant") {
	$plantID = filter_input(INPUT_GET, "plantID");
	getPlant($plantID);
} elseif ($method === "getLeaderboard") {
	getLeaderboard();
} elseif ($method === "getAchievements") {
	$userID = filter_input(INPUT_GET, "userID");
	getAchievements($userID);
} elseif ($method === "getPlantStatus") {
	$plantID = filter_input(INPUT_GET, "plantID");
	getPlantStatus($plantID);
}else {
	http_response_code(400);
	die("400");
}

function uploadData(int $plantID, SensorsData $sensorsData) {
	$mysqli = connect_piante();
	$query = "INSERT INTO geo.data(PLANT_ID, TEMPERATURE, LIGHT, HUMIDITY) VALUES(?, ?, ?, ?)";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("iddd", $plantID, $sensorsData->temperature, $sensorsData->light, $sensorsData->humidity);
	$stmt->execute();
	echo json_encode(["ok"=>true]);
}

function getData(int $plantID) {
	$mysqli = connect_piante();
	$query = "SELECT * FROM geo.data WHERE PLANT_ID = ? ORDER BY UPLOAD_DATE DESC LIMIT 0, 1";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("i", $plantID);
	$stmt->execute();
	echo json_encode($stmt->get_result()->fetch_assoc());
}

function addPlant(int $userID, string $plantName, $plantType) {
	$mysqli = connect_piante();
	$query = "INSERT INTO geo.plants(USER_ID, NAME, TYPE) VALUES(?, ?, ?)";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("isi", $userID, $plantName, $plantType);
	$stmt->execute();
	echo json_encode(["ok"=>true]);
}

function getUser(int $userID) {
	$mysqli = connect_piante();
	$query = "SELECT DESCRIPTION, DAYS FROM geo.badges JOIN badges_assigned ba on badges.ID = ba.BADGE_ID WHERE USER_ID = ?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("i", $userID);
	$badges = [];
	$stmt->execute();
	$result = $stmt->get_result();
	while($row = $result->fetch_assoc())
		$badges[] = $row;

	$query = "SELECT COALESCE(SUM(ADD_POINTS), 0) AS POINTS, NAME, TYPE, START_DATE, END_DATE FROM geo.points RIGHT JOIN plants ON points.PLANT_ID = plants.ID WHERE USER_ID = ?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("i", $userID);
	$plants = [];
	$stmt->execute();
	$result = $stmt->get_result();
	while($row = $result->fetch_assoc())
		$plants[] = $row;

	echo json_encode(["badges"=>$badges, "plants"=>$plants]);
}

function getPlant(int $plantID) {
	$mysqli = connect_piante();
	$query = "SELECT COALESCE(SUM(ADD_POINTS), 0) AS POINTS FROM geo.points WHERE PLANT_ID = ?";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("i", $plantID);
	$stmt->execute();
	echo json_encode($stmt->get_result()->fetch_assoc());
}

function getLeaderboard() {
	$mysqli = connect_piante();
	$query = "SELECT plants.ID AS PLANT_ID, NAME, TYPE, COALESCE(SUM(ADD_POINTS), 0) AS POINTS FROM geo.points RIGHT JOIN plants ON points.PLANT_ID = plants.ID WHERE 1 GROUP BY PLANT_ID, NAME, TYPE";
	$stmt = $mysqli->prepare($query);
	$rows = [];
	$stmt->execute();
	$result = $stmt->get_result();
	while($row = $result->fetch_assoc()) {
		$rows[] = $row;
	}
	echo json_encode($rows);
}

function getAchievements(int $userID) {
	$mysqli = connect_piante();
	$query = "SELECT badges.ID, TITLE, DESCRIPTION, DAYS, ba.DATE_ASSIGNED FROM geo.badges LEFT JOIN badges_assigned ba on badges.ID = ba.BADGE_ID WHERE USER_ID = ? OR USER_ID IS NULL";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("i", $userID);
	$badges = [];
	$stmt->execute();
	$result = $stmt->get_result();
	while($row = $result->fetch_assoc())
		$badges[] = $row;

	echo json_encode($badges);
}

function getPlantStatus(int $plantID) {

}

class SensorsData {
	/**
	 * @return mixed
	 */
	public function getTemperature()
	{
		return $this->temperature;
	}

	/**
	 * @return mixed
	 */
	public function getLight()
	{
		return $this->light;
	}

	/**
	 * @return mixed
	 */
	public function getHumidity()
	{
		return $this->humidity;
	}
	public $temperature, $light, $humidity;

	/**
	 * @param $temperature
	 * @param $light
	 * @param $humidity
	 */
	public function __construct($temperature, $light, $humidity)
	{
		$this->temperature = $temperature;
		$this->light = $light;
		$this->humidity = $humidity;
	}
}