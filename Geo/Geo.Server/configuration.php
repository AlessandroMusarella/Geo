<?php
function connect_piante() {
	$mysqli = new mysqli("127.0.0.1","geo","", "geo");
	$mysqli->set_charset("utf8mb4");
	return $mysqli;
}