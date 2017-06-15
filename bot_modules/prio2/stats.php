<?php

add_to_help("/stats --> Zeigt Statistiken");

function call_stats_help() {
	$text = "Bitte gib an, welche Statistiken du sehen willst:\n";
	$text .= "/stats allwords --> Zeigt Statistiken zu allen Worten\n";
	$text .= "/stats word {Wort} --> Zeigt Statistiken zu einem Wort\n";
	$text .= "/stats common --> Zeigt allgemeine Statistiken\n";
	$text .= "/stats me --> Zeigt Statistiken zu dir\n";
	post_reply($text);
}

if ($glob_switcher == '/stats') {
	if (isset($befehle[1])) {
		switch (strtolower($befehle[1])) {
			case 'allwords':
				$max_lengths = array(0, 0, 0, 0, 0, 0);
				// contents
				$toc = array();

				// heading
				$zeilenarray = array(
					"word" => "Wort",
					"count" => "Anzahl",
					"firstusedby" => "Zuerst von",
					"firstusedat" => "am",
					"lastusedby" => "Zuletzt von",
					"lastusedat" => "am",
				);
				$toc[] = $zeilenarray;

				// get information for every word
				$sql = "SELECT * FROM tb_word_stats ORDER BY word ASC;";
				$result = $mysqli->query($sql);
				while ($row = $result->fetch_object()) {
					// firstseen
					$sql2 = "SELECT username FROM tb_lastseen_users WHERE userid='" . $row->firstusedby . "' LIMIT 1;";
					$result2 = $mysqli->query($sql2);
					while ($row2 = $result2->fetch_object()) {
						$firstseen_username = $row2->username;
					}

					// lastseen
					$sql2 = "SELECT username FROM tb_lastseen_users WHERE userid='" . $row->lastusedby . "' LIMIT 1;";
					$result2 = $mysqli->query($sql2);
					while ($row2 = $result2->fetch_object()) {
						$lastseen_username = $row2->username;
					}

					$zeilenarray = array(
						"word" => $row->word,
						"count" => $row->count,
						"firstusedby" => $firstseen_username,
						"firstusedat" => date("d.m.Y h:i", $row->firstusedat),
						"lastusedby" => $lastseen_username,
						"lastusedat" => date("d.m.Y h:i", $row->lastusedat),
					);
					$toc[] = $zeilenarray;
				}

				// max space for padding
				$maxlengtharray = array(
					"word" => 0,
					"count" => 0,
					"firstusedby" => 0,
					"firstusedat" => 0,
					"lastusedby" => 0,
					"lastusedat" => 0,
				);
				foreach ($toc as $toc_row) {
					if (strlen($toc_row["word"]) > $maxlengtharray["word"]) {
						$maxlengtharray["word"] = strlen($toc_row["word"]);
					}
					if (strlen($toc_row["count"]) > $maxlengtharray["count"]) {
						$maxlengtharray["count"] = strlen($toc_row["count"]);
					}
					if (strlen($toc_row["firstusedby"]) > $maxlengtharray["firstusedby"]) {
						$maxlengtharray["firstusedby"] = strlen($toc_row["firstusedby"]);
					}
					if (strlen($toc_row["firstusedat"]) > $maxlengtharray["firstusedat"]) {
						$maxlengtharray["firstusedat"] = strlen($toc_row["firstusedat"]);
					}
					if (strlen($toc_row["lastusedby"]) > $maxlengtharray["lastusedby"]) {
						$maxlengtharray["lastusedby"] = strlen($toc_row["lastusedby"]);
					}
					if (strlen($toc_row["lastusedat"]) > $maxlengtharray["lastusedat"]) {
						$maxlengtharray["lastusedat"] = strlen($toc_row["lastusedat"]);
					}
				}

				$text = str_pad($toc[0]["word"], $maxlengtharray["word"]) . "|" . str_pad($toc[0]["count"], $maxlengtharray["count"]) . "|" . str_pad($toc[0]["firstusedby"], $maxlengtharray["firstusedby"]) . "|" . str_pad($toc[0]["firstusedat"], $maxlengtharray["firstusedat"]) . "|" . str_pad($toc[0]["lastusedby"], $maxlengtharray["lastusedby"]) . "|" . str_pad($toc[0]["lastusedat"], $maxlengtharray["lastusedat"]) . "\n\n";
				$text .= str_pad("_", $maxlengtharray["word"], "_") . "+" . str_pad("_", $maxlengtharray["count"], "_") . "+" . str_pad("_", $maxlengtharray["firstusedby"], "_") . "+" . str_pad("_", $maxlengtharray["firstusedat"], "_") . "+" . str_pad("_", $maxlengtharray["lastusedby"], "_") . "+" . str_pad("_", $maxlengtharray["lastusedat"], "_") . "\n";

				for ($i = 1; $i < count($toc); $i++) {
					$text .= str_pad($toc[$i]["word"], $maxlengtharray["word"]) . "|" . str_pad($toc[$i]["count"], $maxlengtharray["count"]) . "|" . str_pad($toc[$i]["firstusedby"], $maxlengtharray["firstusedby"]) . "|" . str_pad($toc[$i]["firstusedat"], $maxlengtharray["firstusedat"]) . "|" . str_pad($toc[$i]["lastusedby"], $maxlengtharray["lastusedby"]) . "|" . str_pad($toc[$i]["lastusedat"], $maxlengtharray["lastusedat"]) . "\n";
				}

				post_reply("Insgesamt habe ich " . $result->num_rows . " Wörter gesehen:");
				post_reply($text);
				break;
			case 'word':
				if (isset($befehle[2]) && trim($befehle[2]) != '') {
					$sql = "SELECT * FROM tb_word_stats WHERE word='" . $mysqli->real_escape_string(trim($befehle[2])) . "' LIMIT 1;";
					$result = $mysqli->query($sql);
					$text = '';
					if ($result->num_rows > 0) {
						while ($row = $result->fetch_object()) {
							if ($row->count > 0) {
								// firstseen
								$sql2 = "SELECT username FROM tb_lastseen_users WHERE userid='" . $row->firstusedby . "' LIMIT 1;";
								$result2 = $mysqli->query($sql2);
								while ($row2 = $result2->fetch_object()) {
									$firstseen_username = $row2->username;
								}

								// lastseen
								$sql2 = "SELECT username FROM tb_lastseen_users WHERE userid='" . $row->lastusedby . "' LIMIT 1;";
								$result2 = $mysqli->query($sql2);
								while ($row2 = $result2->fetch_object()) {
									$lastseen_username = $row2->username;
								}

								// render response
								$text .= "Das Wort " . $row->word . " wurde zuerst von " . $firstseen_username . " am " . date("d.m.Y", $row->firstusedat) . " um " . date("H:i", $row->firstusedat) . " verwendet. Zuletzt hat es " . $lastseen_username . " am " . date("d.m.Y", $row->lastusedat) . " um " . date("H:i", $row->lastusedat) . " in den Chat gepostet.\nInsgesamt wurde es " . $row->count . "mal geschrieben.";
							} else {
								$text .= "Ich kenne das Wort " . trim($befehle[2]) . " nicht.";
							}
						}
					} else {
						$text .= "Ich kenne das Wort " . trim($befehle[2]) . " nicht.";
					}
					post_reply($text);
				} else {
					call_stats_help();
				}
				break;

			case 'common':
				post_reply("coming next...");
				break;

			case 'me':
				$text = "Alles, was ich über dich weiß:\n\n";

				$sql = "SELECT * FROM tb_lastseen_users WHERE userid='" . $update["message"]["from"]["id"] . "' LIMIT 1;";
				$result = $mysqli->query($sql);
				while ($row = $result->fetch_object()) {
					$text .= "Deine Telegram-ID ist " . $row->userid . ".\n";
					$text .= "Dein Telegram-Vorname ist " . $row->username . ".\n";
					$text .= "Zuletzt warst du am " . date("d.m.Y", $row->time) . " um " . date("h:i", $row->time) . "Uhr online.\n";
				}
				$text .= "Unsere Chat-ID ist " . $chatID . ".\n";

				$sql = "SELECT COUNT(*) AS anzahl FROM tb_word_stats WHERE firstusedby='" . $update["message"]["from"]["id"] . "';";
				$result = $mysqli->query($sql);
				while ($row = $result->fetch_object()) {
					$text .= "Du hast im Chat " . $row->anzahl . " Wörter zuerst gesagt.\n";
				}
				post_reply($text);
				break;

			default:
				call_stats_help();
				break;
		}
	} else {
		call_stats_help();
	}
}
$hasbeentriggered = true;
