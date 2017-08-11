<?php

setlocale(LC_TIME, "de_DE.utf8");
date_default_timezone_set("Europe/Berlin");

// config
include "config.php";

// functions
include "functions.php";

// read incoming info and grab the chatID
$content = file_get_contents("php://input");
$update = json_decode($content, true);
if (isset($update["message"])) {
	$chatID = $update["message"]["chat"]["id"];
	mysqli_db_connect();
	db_connect();

	logging($chatID, $update);
	update_lastseen($update["message"]["from"]["first_name"], $update["message"]["from"]["id"]);

	foreach (glob("bot_modules/prio1/*.php") as $filename) {
		include $filename;
	}

	// Text
	if (isset($update["message"]["text"])) {
		$befehle = explode(" ", $update["message"]["text"]);
		$glob_switcher = str_replace($bot_atname, "", strtolower($befehle[0]));
		$hasbeentriggered = false;

		foreach (glob("bot_modules/prio2/*.php") as $filename) {
			include $filename;
		}
		foreach (glob("bot_modules/prio3/*.php") as $filename) {
			include $filename;
		}

		if ($glob_switcher == '/help') {
			call_help();
		}
	}

	foreach (glob("bot_modules/prio4/*.php") as $filename) {
		include $filename;
	}

}
