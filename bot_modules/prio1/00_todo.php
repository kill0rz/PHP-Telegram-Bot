<?php

add_to_help("/todohelp --> Hilfe der ToDo-Gruppe");

// ToDo-Gruppe
if (isset($chatID) && ($chatID == $todo_chatID || $chatID == $bottest_chatID)) {
// if (isset($chatID) && ($chatID == $todo_chatID)) {
	if (isset($update["message"]["text"])) {
		$startcommand_tmp = explode(" ", $update["message"]["text"]);
		switch (str_replace($bot_atname, "", strtolower($startcommand_tmp[0]))) {
			case '/todolist':
				post_todotable();
				break;

			case '/settodoowner':
				if (!isset($startcommand_tmp[1]) || !isset($startcommand_tmp[2])) {
					post_reply("Usage: /settodoowner #{TicketID} {Nutzername}");
				} else {
					// todo
				}
				break;

			case '/todohelp':
				$text = '';
				$text .= "Hilfe der ToDo-Gruppe:\n\n";
				$text .= "/help --> Diese Hilfe hier\n";
				$text .= "+ {TEXT} --> Ticket hinzufügen\n";
				$text .= "*# {NUMMER} --> Ticket als 'In Arbeit' kennzeichnen\n";
				$text .= "-# {NUMMER} --> Ticket entfernen\n";
				post_reply($text);
				break;

		}

		$startsign = substr(trim($update["message"]["text"]), 0, 1);
		switch ($startsign) {
			case '+':
				// add todo
				$sql = "INSERT INTO tb_todolist (content,wishby) VALUES ('" . $mysqli->real_escape_string(trim(substr(trim($update["message"]["text"]), 1))) . "','USERID');";
				$mysqli->query($sql);
				post_todotable();
				break;

			case '-':
				// remove todo
				if ($update["message"]["from"]["id"] == $admin_id) {
					// find ticketid
					$tmp = preg_match_all("/#\s?([0-9]*)/", trim($update["message"]["text"]), $matches);
					$ticketID = $matches[1][0];

					$sql = "SELECT * FROM tb_todolist WHERE ID=" . intval($ticketID) . " LIMIT 1; --";
					$result = $mysqli->query($sql);
					if ($result->num_rows == 0) {
						post_reply("Ticket wurde nicht gefunden.");
					} else {
						while ($row = $result->fetch_object()) {
							if ($row->isactive == 1) {
								// set inactive
								$sql = "UPDATE tb_todolist SET isactive = 0, isintodo = 0 WHERE ID=" . intval($ticketID) . "; --";
								$mysqli->query($sql);
								$result_atname = $mysqli->query("SELECT atname FROM tb_lastseen_users JOIN tb_todolist ON tb_todolist.wishby = tb_lastseen_users.userid WHERE tb_todolist.ID='" . intval($ticketID) . "';");
								$atname = $result_atname->fetch_array();
								$atname = (isset($atname['atname']) && trim($atname['atname']) != '') ? "@" . $atname['atname'] : '';
								post_todotable("Ticket #" . intval($ticketID) . " (\"" . $row->content . "\") wurde geschlossen! " . $atname);
							} else {
								// is inactive
								post_reply("Ticket bereits abgearbeitet!");
							}
						}
					}
				} else {
					post_reply("Sorry, das darf nur der Admin!");
				}
				break;

			case '*':
				// In ToDo nehmen
				if ($update["message"]["from"]["id"] == $admin_id) {
					// find ticketid
					$tmp = preg_match_all("/#\s?([0-9]*)/", trim($update["message"]["text"]), $matches);
					$ticketID = $matches[1][0];

					$sql = "SELECT * FROM tb_todolist WHERE ID=" . intval($ticketID) . " LIMIT 1; --";
					$result = $mysqli->query($sql);
					if ($result->num_rows == 0) {
						post_reply("Ticket wurde nicht gefunden.");
					} else {
						while ($row = $result->fetch_object()) {
							if ($row->isactive == 1) {
								// set inactive
								$sql = "UPDATE tb_todolist SET isintodo = 1 WHERE ID=" . intval($ticketID) . "; --";
								$mysqli->query($sql);
								$result_atname = $mysqli->query("SELECT atname FROM tb_lastseen_users JOIN tb_todolist ON tb_todolist.wishby = tb_lastseen_users.userid WHERE tb_todolist.ID='" . intval($ticketID) . "';");
								$atname = $result_atname->fetch_array();
								$atname = (isset($atname['atname']) && trim($atname['atname']) != '') ? "@" . $atname['atname'] : '';
								post_todotable("Ticket #" . intval($ticketID) . " (\"" . $row->content . "\") ist nun in Arbeit. " . $atname);
							} else {
								// is inactive
								post_reply("Ticket bereits abgearbeitet!");
							}
						}
					}
				} else {
					post_reply("Sorry, das darf nur der Admin!");
				}
				break;
		}
	}
	die();
}