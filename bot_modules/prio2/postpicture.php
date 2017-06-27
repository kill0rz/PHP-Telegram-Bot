<?php

/*
Integration in Woltlab Burning Board 2 - not usable in any other way without modifications
 */

add_to_help("/nextpic --> Bearbeite Bilder fürs Forum");

function call_postpicture_help() {
	$text = "Bitte gib an, was genau du machen willst:\n";
	$text .= "/nextpic --> bearbeite das nächste Bild\n";
	$text .= "/delpic --> lösche aktuelles Bild\n";
	$text .= "/rotatepicright --> Bild rechtsherum drehen\n";
	$text .= "/rotatepicleft --> Bild linksherum drehen\n";
	$text .= "/postall --> Schreibe alle Bilder ins Forum\n";
	$text .= "/help --> Allgemeine Hilfe\n";
	post_reply($text);
}

if ($glob_switcher == '/postall') {
	if ($update["message"]["from"]["id"] == $admin_id) {
		$done_counter = 0;
		$albenurl = $url2board_fotoalbum;

		// Schließe die Bearbeitung aller Bilder:
		$sql = "UPDATE tb_pictures_queue SET current=0;";
		$mysqli->query($sql);

		// Hole zuerst alle Topic-Names
		$sql = "SELECT threadname FROM tb_pictures_queue WHERE TRIM(threadname) IS NOT NULL GROUP BY threadname";
		$result = $mysqli->query($sql);
		while ($thread = $result->fetch_object()) {
			// prüfen, ob der Thread schon existiert
			$usenumber = 0;
			get_thread($thread->threadname);

			// Jetzt nach Nutzernamen gruppieren
			$sql3 = "SELECT q.postedby,u.username,u.wbb_userid FROM tb_pictures_queue q JOIN tb_lastseen_users u ON q.postedby=u.userid GROUP BY postedby;";
			$result3 = $mysqli->query($sql3);
			while ($queue = $result3->fetch_object()) {
				unset($links);
				unset($trigger_poster_name);
				unset($pic_userid);
				unset($wbb_userid);
				$links = '';
				$trigger_poster_name = true;

				if (isset($queue->wbb_userid) && trim($queue->wbb_userid) != '') {
					$pic_userid = $queue->wbb_userid;
					$userid_set = true;
				} else {
					$pic_userid = $bot_userid;
					$userid_set = false;
				}

				// für jedes Bild
				$sql2 = "SELECT * FROM tb_pictures_queue WHERE TRIM(threadname)='" . $thread->threadname . "' AND postedby='" . $queue->postedby . "'";
				$result2 = $mysqli->query($sql2);
				while ($queue_pic = $result2->fetch_object()) {
					if (!$userid_set && $trigger_poster_name) {
						$links .= "Bilder von " . $queue->username . ":\n";
						$trigger_poster_name = false;
					}

					$thema = strtr(strtolower(trim($thread->threadname)), $ersetzen);

					// ggf. Ordner erstellen und directory listing verhindern
					if (!is_dir($subordner)) {
						mkdir($subordner, 0777);
					}
					if (!is_dir($subordner . "/" . $pic_userid)) {
						mkdir($subordner . "/" . $pic_userid, 0777);
					}
					if (!is_dir($subordner . "/" . $pic_userid . "/" . $thema)) {
						mkdir($subordner . "/" . $pic_userid . "/" . $thema, 0777);
					}
					makeindex($subordner . "/" . $pic_userid . "/" . $thema . "/");
					makeindex($subordner . "/" . $pic_userid . "/");
					makeindex($subordner . "/");
					$umaskold = umask(0);

					// allow to randompic of the week
					if ($config_always_allow_randompic) {
						try {
							file_put_contents($subordner . "/" . $pic_userid . "/" . $thema . "/allowtorandompic", "");
						} catch (Exception $e) {
						}
					}

					$DateiName = strtr($queue_pic->filename, $ersetzen);
					while (file_exists($subordner . "/" . $pic_userid . "/" . $thema . "/" . $DateiName)) {
						sleep(1);
						$DateiName = time() . $DateiName;
					}
					resizeImage("./img/" . $queue_pic->location, $subordner . "/" . $pic_userid . "/" . $thema . "/" . $DateiName, 1300, 1, 1);
					$links .= "[IMG]" . $albenurl . "/" . $pic_userid . "/" . $thema . "/" . $DateiName . "[/IMG]\n";

					// remove pic from queue
					$sql3 = "DELETE FROM tb_pictures_queue WHERE id='" . $queue_pic->id . "'";
					$mysqli->query($sql3);
					@unlink("./img/" . $queue_pic->location);
					$done_counter++;
				}

				if (isset($links) && trim($links) != '') {

					// VGPOST bei Viktor - v-gn.de *Anfang*
					$time = time();

					/* Thread erstellen */
					$posting_thema = $thread->threadname;
					$posting_prefix = 'Telegram';

					/* Username holen */
					$user_info = query_first($db, "SELECT username FROM bb" . $n . "_users WHERE userid = '" . $pic_userid . "'");
					$vgp_username = $user_info['username'];

					// Thread schon vorhanden oder neuen erstellen?
					if ($usenumber == 0) {
						// neuer Thread
						$subjekt = $posting_thema;

						$db->query("INSERT INTO bb" . $n . "_threads (boardid,prefix,topic,iconid,starttime,starterid,starter,lastposttime,lastposterid,lastposter,attachments,pollid,important,visible)
									VALUES ('" . $bot_boardid . "', '" . addslashes($posting_prefix) . "', '" . addslashes($posting_thema) . "', '0', '" . $time . "', '" . $pic_userid . "', '" . addslashes($user_info['username']) . "', '" . $time . "', '" . $pic_userid . "', '" . addslashes($user_info['username']) . "', '0', '0', '0', '1')");
						$usenumber = $db->insert_id;
						$threadid = $db->insert_id;
						$hasbeenpostetasareply = false;
						post_reply("Erstelle neuen Thread: " . $posting_thema);
					} else {
						// antworte auf
						$threadid = $usenumber;
						$subjekt = "[" . $posting_prefix . "] " . $posting_thema;
						$hasbeenpostetasareply = true;

						post_reply("Antworte auf Thread: " . utf8_decode($subjekt));
					}

					$b_thread = trim($links);

					/* Post erstellen */
					$db->query("INSERT INTO bb" . $n . "_posts (threadid,userid,username,iconid,posttopic,posttime,message,attachments,allowsmilies,allowhtml,allowbbcode,allowimages,showsignature,ipaddress,visible) VALUES ('" . $threadid . "', '" . $pic_userid . "', '" . addslashes($user_info['username']) . "', '0', '" . addslashes($subjekt) . "', '" . $time . "', '" . addslashes($b_thread) . "', '0', '1', '0', '1', '1', '1', '127.0.0.1', '1')");
					$postid = $db->insert_id;

					/* Board updaten */
					$boardstr = query_first($db, "SELECT parentlist FROM bb" . $n . "_boards WHERE boardid = '" . $bot_boardid . "'");
					$parentlist = $boardstr['parentlist'];

					/* update thread info */
					$db->query("UPDATE bb" . $n . "_threads SET lastposttime = '" . $time . "', lastposterid = '" . $pic_userid . "', lastposter = '" . addslashes($user_info['username']) . "', replycount = replycount+1 WHERE threadid = '{$threadid}'", 1);

					/* update board info */
					$db->query("UPDATE bb" . $n . "_boards SET postcount=postcount+1, lastthreadid='{$threadid}', lastposttime='" . $time . "', lastposterid='" . $pic_userid . "', lastposter='" . addslashes($user_info['username']) . "' WHERE boardid IN ({$parentlist},{$bot_boardid})", 1);

					$db->query("UPDATE bb" . $n . "_users SET userposts=userposts+1 WHERE userid = '" . $pic_userid . "'", 1);

					/* Statistik updaten */
					if ($hasbeenpostetasareply) {
						$db->query("UPDATE bb" . $n . "_stats SET threadcount=threadcount+1, postcount=postcount+1", 1);
					} else {
						$db->query("UPDATE bb" . $n . "_stats SET postcount=postcount+1", 1);
					}
					// VGPOST bei Viktor - v-gn.de *Anfang*
				}
			}
		}
		post_reply("Es wurden " . $done_counter . " Bilder ins Forum gepostet.");
		// call_postpicture_help();
	} else {
		post_reply("Sorry, das darf nur der Admin!");
	}
}

if ($glob_switcher == '/nextpic') {
	// hole das nächste Bild aus der Queue und lege den Threadnamen fest

	if ($update["message"]["from"]["id"] == $admin_id) {

		// Prüfen, ob mehr als 0 Bilder zum abarbeiten da sind
		$sql = "SELECT * FROM tb_pictures_queue WHERE TRIM(threadname) IS NULL ORDER BY id ASC LIMIT 1;";
		$result = $mysqli->query($sql);
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_object()) {
				// check, ob bereits ein Pic in der Queue ist
				$sql2 = "SELECT * FROM tb_pictures_queue WHERE TRIM(threadname) IS NULL AND current=1 LIMIT 1;";
				$result2 = $mysqli->query($sql2);
				if ($result2->num_rows > 0) {
					while ($row2 = $result2->fetch_object()) {
						$id = $row2->id;
					}
				} else {
					$id = $row->id;
				}

				// set current
				$sql3 = "UPDATE tb_pictures_queue SET current=1 WHERE id='" . $id . "'";
				$mysqli->query($sql3);

				$sql2 = "SELECT q.*,u.username FROM tb_pictures_queue q JOIN tb_lastseen_users u ON q.postedby=u.userid WHERE TRIM(threadname) IS NULL AND current=1 LIMIT 1;";
				$result2 = $mysqli->query($sql2);
				while ($row2 = $result2->fetch_object()) {
					send_photo($row2->telegramfileid);
					$sql3 = "SELECT topicname FROM tb_set_topic LIMIT 1;";
					$result3 = $mysqli->query($sql3);
					while ($row3 = $result3->fetch_object()) {
						$oldname = $row3->topicname;
					}
					$text = "Gepostet von " . $row2->username . " am " . date("d.m.Y", $row2->postedat) . " um " . date("H:i", $row2->postedat) . "Uhr.\n";
					$text .= "In welches Thema soll ich das Bild posten?\n/settopic {Name} [" . $oldname . "]\n/delpic --> Bild löschen";
					post_reply($text);
				}
			}
		} else {
			post_reply("Es gibt derzeit keine Bilder, die abgearbeitet werden können.");
			call_postpicture_help();
		}
	} else {
		post_reply("Sorry, das darf nur der Admin!");
	}

}
if ($glob_switcher == '/delpic') {
	if ($update["message"]["from"]["id"] == $admin_id) {
		$sql = "SELECT id, location FROM tb_pictures_queue WHERE current=1 AND TRIM(threadname) IS NULL LIMIT 1;";
		$result = $mysqli->query($sql);
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_object()) {
				// Es gibt ein Bild, dass gelöscht werden kann
				// --> Löschen des Tupel
				$sql2 = "DELETE FROM tb_pictures_queue WHERE id='" . $row->id . "';";
				$mysqli->query($sql2);
				@unlink("./img/" . $row->location);
				post_reply("Bild erfolgreich gelöscht!\n/nextpic");
			}
		} else {
			post_reply("Es befindet sich kein Bild in der Queue.");
			call_postpicture_help();
		}
	} else {
		post_reply("Sorry, das darf nur der Admin!");
	}

}
if ($glob_switcher == '/rotatepicleft') {
	if ($update["message"]["from"]["id"] == $admin_id) {
		$sql = "SELECT q.*,u.username  FROM tb_pictures_queue q JOIN tb_lastseen_users u ON q.postedby=u.userid WHERE current=1 AND TRIM(threadname) IS NULL LIMIT 1;";
		$result = $mysqli->query($sql);
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_object()) {
				// Es gibt ein Bild, dass abgearbeitet werden kann

				RotateJpg("./img/" . $row->location, 90);
				$uniquefilename = time() . rand(1, 1000);
				if (copy("./img/" . $row->location, "./img_tmp/" . $uniquefilename . ".jpg")) {
					send_photo($url2bot . "/img_tmp/" . $uniquefilename . ".jpg");
					unlink("./img_tmp/" . $uniquefilename . ".jpg");
				}

				$sql3 = "SELECT topicname FROM tb_set_topic LIMIT 1;";
				$result3 = $mysqli->query($sql3);
				while ($row3 = $result3->fetch_object()) {
					$oldname = $row3->topicname;
				}
				$text = "Gepostet von " . $row->username . " am " . date("d.m.Y", $row->postedat) . " um " . date("H:i", $row->postedat) . "\n";
				afterpic_opertaions();
			}
		} else {
			post_reply("Es befindet sich kein Bild in der Queue.");
			call_postpicture_help();
		}
	} else {
		post_reply("Sorry, das darf nur der Admin!");
	}

}
if ($glob_switcher == '/rotatepicright') {
	if ($update["message"]["from"]["id"] == $admin_id) {
		$sql = "SELECT q.*,u.username  FROM tb_pictures_queue q JOIN tb_lastseen_users u ON q.postedby=u.userid WHERE current=1 AND TRIM(threadname) IS NULL LIMIT 1;";
		$result = $mysqli->query($sql);
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_object()) {
				// Es gibt ein Bild, dass abgearbeitet werden kann

				RotateJpg("./img/" . $row->location, 270);
				$uniquefilename = time() . rand(1, 1000);
				if (copy("./img/" . $row->location, "./img_tmp/" . $uniquefilename . ".jpg")) {
					send_photo($url2bot . "/img_tmp/" . $uniquefilename . ".jpg");
					unlink("./img_tmp/" . $uniquefilename . ".jpg");
				}

				$sql3 = "SELECT topicname FROM tb_set_topic LIMIT 1;";
				$result3 = $mysqli->query($sql3);
				while ($row3 = $result3->fetch_object()) {
					$oldname = $row3->topicname;
				}
				$text = "Gepostet von " . $row->username . " am " . date("d.m.Y", $row->postedat) . " um " . date("H:i", $row->postedat) . "\n";
				afterpic_opertaions();
			}
		} else {
			post_reply("Es befindet sich kein Bild in der Queue.");
			call_postpicture_help();
		}
	} else {
		post_reply("Sorry, das darf nur der Admin!");
	}

}
if ($glob_switcher == '/settopic') {
	if ($update["message"]["from"]["id"] == $admin_id) {
		if (!isset($befehle[1]) || trim($befehle[1]) == '') {
			$sql = "SELECT topicname FROM tb_set_topic LIMIT 1;";
			$result = $mysqli->query($sql);
			while ($row = $result->fetch_object()) {
				post_reply("Kein Thema angegeben, ich nehme das letzte: " . $row->topicname);
				$topic = $row->topicname;
			}
		} else {
			$topic = '';
			for ($i = 1; $i < count($befehle); $i++) {
				$topic .= " " . $befehle[$i];
			}
			$topic = trim($topic);
		}

		$sql = "SELECT id FROM tb_pictures_queue WHERE current=1 AND TRIM(threadname) IS NULL LIMIT 1;";
		$result = $mysqli->query($sql);
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_object()) {
				// Wir kennen das Thema und es gibt ein Bild, dass das Thema erwartet
				// --> Update des Tupel
				$sql2 = "UPDATE tb_pictures_queue SET threadname='" . $mysqli->real_escape_string($topic) . "', current=0 WHERE id='" . $row->id . "';";
				$mysqli->query($sql2);
				$sql2 = "UPDATE tb_set_topic SET topicname='" . $mysqli->real_escape_string($topic) . "';";
				$mysqli->query($sql2);
				post_reply("Thema erfolgreich gesetzt!");
				call_postpicture_help();
			}
		} else {
			post_reply("Es befindet sich kein Bild in der Queue.");
		}
	} else {
		post_reply("Sorry, das darf nur der Admin!");
	}
}
