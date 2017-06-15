<?php

if ($glob_switcher == '/startvote') {
	// check if is admin
	if ($update["message"]["from"]["id"] == $admin_id) {
		// check if there is a vote already
		$sql = "SELECT * FROM tb_vote_options;";
		$result = $mysqli->query($sql);
		if ($result->num_rows > 0) {
			post_reply("Es gibt bereits eine Abstimmung. Bitte schließe diese zuerst!\n/closevote");
		} else {
			// check if all params are set
			// unique array at first
			$befehle = array_unique($befehle);
			if (count($befehle) > 2) {
				$text = "Hey Leute,\nihr müsst jetzt abstimmen!\n";
				for ($i = 1; $i < count($befehle); $i++) {
					// insert each into db
					$sql = "INSERT INTO tb_vote_options (vote_option) VALUES('" . $mysqli->real_escape_string($befehle[$i]) . "')";
					$mysqli->query($sql);
					$text .= "Stimmst du für [[" . $befehle[$i] . "]], dann schreibe\n/vote " . $befehle[$i] . "\n\n";
				}
				// $text .= "Möge der Bessere gewinnen!";
				post_reply($text);
			} else {
				post_reply("Du musst mindestens zwei Optionen angeben!");
			}
		}
	} else {
		post_reply("Sorry, das darf nur der Admin!");
	}
}

if ($glob_switcher == '/voteintermediateresult') {
	// print stats
	$sql = "SELECT tb_vote_options.vote_option, COUNT(tb_vote_options.vote_option) AS count FROM tb_votes JOIN tb_vote_options ON tb_vote_options.ID=tb_votes.vote_option_id GROUP BY vote_option_id ORDER BY count DESC;";
	$result = $mysqli->query($sql);
	$count = 0;
	$merk = 0;

	$text = "Der derzeitige Zwischenstand sieht so aus:\n\n";
	while ($row = $result->fetch_object()) {
		// test if Gleichstand
		if ($merk != $row->count) {
			$count++;
		}
		$text .= "Platz " . $count . ": " . $row->vote_option . " mit " . $row->count . " Stimmen,\n";
		$merk = $row->count;
	}
	$text = substr($text, 0, -2);
	post_reply($text);
}
if ($glob_switcher == '/closevote') {
	if ($update["message"]["from"]["id"] == $admin_id) {
		// check if there is a vote already
		$sql = "SELECT * FROM tb_vote_options;";
		$result = $mysqli->query($sql);
		if ($result->num_rows == 0) {
			post_reply("Es gibt keine Abstimmung. Bitte eröffne eine neue!\n/startvote");
		} else {
			// print stats
			$sql = "SELECT tb_vote_options.vote_option, COUNT(tb_vote_options.vote_option) AS count FROM tb_votes JOIN tb_vote_options ON tb_vote_options.ID=tb_votes.vote_option_id GROUP BY vote_option_id ORDER BY count DESC;";
			$result = $mysqli->query($sql);
			$count = 0;
			$merk = 0;

			$text = "Ergebis der letzten Abstimmung:\n\n";
			while ($row = $result->fetch_object()) {
				// test if Gleichstand
				if ($merk != $row->count) {
					$count++;
				}
				$text .= "Platz " . $count . ": " . $row->vote_option . " mit " . $row->count . " Stimmen,\n";
				$merk = $row->count;
			}
			$text = substr($text, 0, -2);
			post_reply($text);

			//reset everything
			$sql = "DELETE FROM tb_votes;";
			$mysqli->query($sql);
			$sql = "DELETE FROM tb_vote_options;";
			$mysqli->query($sql);
			post_reply("Erfolgreich zurückgesetzt!\nNeue Abstimmung mit /startvote");
		}
	} else {
		post_reply("Sorry, das darf nur der Admin!");
	}
}
if ($glob_switcher == '/vote') {
	// test of voteoption isset
	if (isset($befehle[1])) {
		// test, if vote already given
		$sql = "SELECT ID FROM tb_votes WHERE telegram_id='" . $update["message"]["from"]["id"] . "';";
		$result = $mysqli->query($sql);
		if ($result->num_rows > 0) {
			post_reply("Sorry, du hast schon abgestimmt!");
		} else {
			// test if voteoption is valid
			$sql = "SELECT ID FROM tb_vote_options WHERE vote_option='" . $mysqli->real_escape_string(trim($befehle[1])) . "' LIMIT 1;";
			$result = $mysqli->query($sql);
			if ($result->num_rows == 1) {
				// vote is valid, save it
				while ($row = $result->fetch_object()) {
					$sql = "INSERT INTO tb_votes (vote_option_id, telegram_id) VALUES('" . $row->ID . "', '" . trim($update["message"]["from"]["id"]) . "');";
					$mysqli->query($sql);
				}
				post_reply("Stimme erfolgreich gespeichert!");
			} else {
				post_reply("Tut mir leid, aber diese Option steht nicht zur Auswahl!");
			}
		}
	} else {
		post_reply("Du musst angeben, wem du die Stimme geben willst:\n/vote Option");
	}
}
$hasbeentriggered = true;