<?php

add_to_help("/lastseen {NUTZERNAME} --> Zeigt dir an, wann der Nutzer das letzte mal online war.");

if ($glob_switcher == '/lastseen') {
	if (count($befehle) > 1) {
		for ($i = 1; $i < count($befehle); $i++) {
			if (strtolower($befehle[$i]) == strtolower($update["message"]["from"]["first_name"])) {
				$text = "Willst du mich rollen? Du bist online! ;(";
				post_reply($text);
			} else {
				$sql = "SELECT * FROM tb_lastseen_users WHERE LOWER(username)='" . strtolower($mysqli->real_escape_string($befehle[$i])) . "' ORDER BY id ASC LIMIT 1";
				$result = $mysqli->query($sql);
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_object()) {
						$text = "Hallo " . $update["message"]["from"]["first_name"] . ",\nich habe " . $row->username . " zuletzt am " . strftime("%A", $row->time) . ", dem " . date("d.m.Y", $row->time) . " um " . date("H:i", $row->time) . "Uhr gesehen.";
						post_reply($text);
					}
				} else {
					$text = "Tut mir leid, " . $befehle[$i] . " kenne ich nicht.";
					post_reply($text);
				}
			}
		}
	} else {
		post_reply("Du musst einen Nutzer angeben, etwa so: /lastseen " . $example_name);
	}
}
$hasbeentriggered = true;
