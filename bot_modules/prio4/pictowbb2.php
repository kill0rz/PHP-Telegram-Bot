<?php

// check if file is attached
if (isset($update["message"]["photo"]) && @isset($update["message"]["photo"][count($update["message"]["photo"]) - 1]["file_id"])) {
	// komprimierte Bilder
	// check if facefilter is triggered
	if (@isset($update["message"]["caption"]) && trim($update["message"]["caption"]) != '' && substr($update["message"]["caption"], 0, 5) === "/face") {
		// download file
		$sendto = API_URL . "getfile?file_id=" . $update["message"]["photo"][count($update["message"]["photo"]) - 1]["file_id"];
		$resp = json_decode(file_get_contents($sendto), true);
		$file_path = $resp["result"]["file_path"];

		$sendto = API_URL_FILE . $file_path;
		$savename = "facefile.jpg";
		$savename_rand = time() . rand(0, 1000) . rand(0, 1000) . rand(0, 1000) . rand(0, 1000) . ".jpg";

		if (file_put_contents("img/" . $savename, file_get_contents($sendto))) {
			// downloaded the pic; now process it
			post_reply("Sekunde...");
			@shell_exec("cd ./chrisify && ./chrisify ../img/" . $savename . " > ../img_tmp/" . $savename_rand);
			send_photo($url2bot . "/img_tmp/" . $savename_rand);
			unlink("./img_tmp/" . $savename_rand);
			unlink("./img/" . $savename);
		} else {
			post_reply("Es gab leider einen Fehler beim Download des Bildes! :( @" . $admin_name);
		}
	} else {
		$file_id = $update["message"]["photo"][count($update["message"]["photo"]) - 1]["file_id"];
		$filename = isset($update["message"]["photo"][count($update["message"]["photo"]) - 1]["file_path"]) ? str_replace("photo/", "", $update["message"]["photo"][count($update["message"]["photo"]) - 1]["file_path"]) : time() . ".jpg";
	}
} elseif (isset($update["message"]["document"]["file_id"])) {
	// unkomprimierte Bilder und Dateianhänge
	if (strtolower(substr($update["message"]["document"]["mime_type"], 0, 5)) == "image") {
		$filename = $update["message"]["document"]["file_name"];
		$file_id = $update["message"]["document"]["file_id"];
	}
}

// if so, put it into DB
if (isset($file_id) && isset($filename)) {
	// get info
	$sendto = API_URL . "getfile?file_id=" . $file_id;
	$resp = json_decode(file_get_contents($sendto), true);
	$file_path = $resp["result"]["file_path"];

	$sendto = API_URL_FILE . $file_path;
	$savename = time() . rand(0, 1000) . rand(0, 1000) . rand(0, 1000) . rand(0, 1000);
	if (file_put_contents("img/" . $savename, file_get_contents($sendto))) {
		$sql = "INSERT INTO tb_pictures_queue (filename, location, telegramfileid, postedby, postedat) VALUES('" . $mysqli->real_escape_string($filename) . "', '" . $savename . "', '" . $file_id . "', '" . $mysqli->real_escape_string($update["message"]["from"]["id"]) . "', '" . time() . "');";
		$mysqli->query($sql);

		// compose reply
		// post_reply("Danke, " . $update["message"]["from"]["first_name"] . "! Das Bild wurde für das Forum vorgemerkt!");
	} else {
		post_reply("Es gab leider einen Fehler beim vormerken des Bildes! :( @" . $admin_name);
	}
}