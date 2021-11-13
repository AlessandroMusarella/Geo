<?php

require_once "configuration.php";

$method = $_GET["method"];


if($method === "upload") {
	$clientID = filter_input(INPUT_GET, "clientID");
	$temperature = filter_input(INPUT_GET, "temperature");
	$light = filter_input(INPUT_GET, "light");
	$humidity = filter_input(INPUT_GET, "humidity");
	$sensorsData = new SensorsData($temperature, $light, $humidity);
    uploadData($clientID, $sensorsData);
} elseif ($method === "get") {
	$clientID = filter_input(INPUT_GET, "clientID");
    getData($clientID);
}

function uploadData(int $clientID, SensorsData $sensorsData) {
	$mysqli = connect_piante();
	$query = "INSERT INTO geo.data(CLIENT_ID, TEMPERATURE, LIGHT, HUMIDITY) VALUES(?, ?, ?, ?)";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("iddd", $clientID, $sensorsData->temperature, $sensorsData->light, $sensorsData->humidity);
	$stmt->execute();
	echo json_encode(["ok"=>true]);
}

function getData(int $clientID) {
	$mysqli = connect_piante();
	$query = "SELECT * FROM geo.data WHERE CLIENT_ID = ? ORDER BY UPLOAD_DATE DESC LIMIT 0, 1";
	$stmt = $mysqli->prepare($query);
	$stmt->bind_param("i", $clientID);
	$stmt->execute();
	echo json_encode($stmt->get_result()->fetch_assoc());
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