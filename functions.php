<?php

$ersetzen = array('ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'Ä' => 'ae', 'Ö' => 'oe', 'Ü' => 'ue', 'ß' => 'ss', ' ' => '_', '\\' => '-', '/' => '-', "http://" => "", "http" => "", "//" => "", ":" => "", ";" => "", "[" => "", "]" => "", "{" => "", "}" => "", "%" => "", "$" => "", "?" => "", "!" => "", "=" => "", "'" => "_", "(" => "_", ")" => "_");
$helptext = array();

function logging($chatID, $update) {
	$myFile = "log.txt";
	$updateArray = print_r($update, TRUE);
	$fh = fopen($myFile, 'a') or die("can't open file");
	fwrite($fh, $chatID . "\n\n");
	fwrite($fh, $updateArray . "\n\n");
	fclose($fh);
}

function post_reply($reply) {
	global $chatID;
	$sendto = API_URL . "sendmessage?chat_id=" . $chatID . "&text=" . urlencode($reply);
	file_get_contents($sendto);
}

function send_photo($fileid) {
	global $chatID;
	$sendto = API_URL . "sendphoto?chat_id=" . $chatID . "&photo=" . urlencode($fileid);
	file_get_contents($sendto);
}

function send_video($fileid) {
	global $chatID;
	$sendto = API_URL . "sendvideo?chat_id=" . $chatID . "&video=" . urlencode($fileid);
	file_get_contents($sendto);
}

function send_document($fileid) {
	global $chatID;
	$sendto = API_URL . "senddocument?chat_id=" . $chatID . "&document=" . urlencode($fileid);
	file_get_contents($sendto);
}
function send_sticker($fileid) {
	global $chatID;
	$sendto = API_URL . "sendsticker?chat_id=" . $chatID . "&sticker=" . urlencode($fileid);
	file_get_contents($sendto);
}

function update_lastseen($username, $userid) {
	global $mysqli;
	if (trim($username) != '') {
		$sql = "INSERT INTO tb_lastseen_users (userid, time, username) VALUES('" . $mysqli->real_escape_string($userid) . "', '" . time() . "', '" . $username . "') ON DUPLICATE KEY UPDATE time=" . time() . ", username='" . $username . "';";
		$mysqli->query($sql);
	}
}

function insert_or_update_word($word, $username) {
	global $mysqli;

	if (trim($word) != '' && preg_match("/[a-zA-Z]*/", $word)) {
		$word = preg_replace('/\PL/u', '', $word);
		$sql = "INSERT INTO tb_word_stats (word, firstusedby, firstusedat, lastusedby,lastusedat) VALUES('" . $mysqli->real_escape_string($word) . "','" . $mysqli->real_escape_string($username) . "','" . time() . "', '" . $mysqli->real_escape_string($username) . "', '" . time() . "') ON DUPLICATE KEY UPDATE count=count+1, lastusedby='" . $mysqli->real_escape_string($username) . "', lastusedat='" . time() . "';";
		$mysqli->query($sql);
	}
}

function afterpic_opertaions() {
	global $text, $oldname;
	$text .= "In welches Thema soll ich das Bild posten?\n";
	$text .= "/settopic {Name} [" . $oldname . "]\n";
	$text .= "/rotatepicright --> Bild rechtsrum drehen\n";
	$text .= "/rotatepicleft --> Bild linksrum drehen\n";
	$text .= "/delpic --> Bild löschen";
	post_reply($text);
}

function resizeImage($filepath_old, $filepath_new, $image_dimension, $scale_mode = 0, $overwrite = 0) {
	if ($overwrite == 1) {
		if (!(file_exists($filepath_old))) {
			return false;
		}

	} else {
		if (!(file_exists($filepath_old)) || file_exists($filepath_new)) {
			return false;
		}

	}

	$image_attributes = getimagesize($filepath_old);
	$image_width_old = $image_attributes[0];
	$image_height_old = $image_attributes[1];
	$image_filetype = $image_attributes[2];

	if ($image_width_old <= $image_dimension || !(isset($_POST['compress']) && $_POST['compress'] == "true")) {
		if (copy($filepath_old, $filepath_new)) {
			return true;
		} else {
			return false;
		}
	}

	if ($image_width_old <= 0 || $image_height_old <= 0) {
		return false;
	}

	$image_aspectratio = $image_width_old / $image_height_old;

	if ($scale_mode == 0) {
		$scale_mode = ($image_aspectratio > 1 ? -1 : -2);
	} elseif ($scale_mode == 1) {
		$scale_mode = ($image_aspectratio > 1 ? -2 : -1);
	}

	if ($scale_mode == -1) {
		$image_width_new = $image_dimension;
		$image_height_new = round($image_dimension / $image_aspectratio);
	} elseif ($scale_mode == -2) {
		$image_height_new = $image_dimension;
		$image_width_new = round($image_dimension * $image_aspectratio);
	} else {
		return false;
	}

	switch ($image_filetype) {
		case 1:
			$image_old = imagecreatefromgif($filepath_old);
			$image_new = imagecreate($image_width_new, $image_height_new);
			imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $image_width_new, $image_height_new, $image_width_old, $image_height_old);
			imagegif($image_new, $filepath_new);
			break;

		case 2:
			$image_old = @imagecreatefromjpeg($filepath_old);
			$image_new = imagecreatetruecolor($image_width_new, $image_height_new);
			imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $image_width_new, $image_height_new, $image_width_old, $image_height_old);
			imagejpeg($image_new, $filepath_new);
			break;

		case 3:
			$image_old = imagecreatefrompng($filepath_old);
			$image_colordepth = imagecolorstotal($image_old);

			if ($image_colordepth == 0 || $image_colordepth > 255) {
				$image_new = imagecreatetruecolor($image_width_new, $image_height_new);
			} else {
				$image_new = imagecreate($image_width_new, $image_height_new);
			}

			imagealphablending($image_new, false);
			imagecopyresampled($image_new, $image_old, 0, 0, 0, 0, $image_width_new, $image_height_new, $image_width_old, $image_height_old);
			imagesavealpha($image_new, true);
			imagepng($image_new, $filepath_new);
			break;

		default:
			return false;
	}

	imagedestroy($image_old);
	imagedestroy($image_new);
	return true;
}

function parse_dateformats($ordnername) {
	$mode = 0;
	preg_match_all("/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/", $ordnername, $matches);
	if (count($matches[0])) {
		$mode = 1;
	} else {
		preg_match_all("/[0-9]{2}\.[0-9]{2}\.[0-9]{2}/", $ordnername, $matches);
		if (count($matches[0])) {
			$mode = 2;
		} else {
			preg_match_all("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", $ordnername, $matches);
			if (count($matches[0])) {
				$mode = 3;
			}
		}
	}

	if ($mode > 0) {
		switch ($mode) {
			case 1:
				$teile = explode(".", $matches[0][0]);
				$year = $teile[2];
				$month = $teile[1];
				$day = $teile[0];
				if (preg_replace("/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/", "", $ordnername) == "") {
					return $ordnername;
				} else {
					if (substr(preg_replace("/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/", "", $ordnername), 0, 1) == "_") {
						return substr(preg_replace("/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/", "", $ordnername), 1) . "_" . $day . "." . $month . "." . $year;
					} else {
						return substr(preg_replace("/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/", "", $ordnername), 0, -1) . "_" . $day . "." . $month . "." . $year;
					}
				}
			case 2:
				$teile = explode(".", $matches[0][0]);
				$year = "20" . $teile[2];
				$month = $teile[1];
				$day = $teile[0];
				if (preg_replace("/[0-9]{2}\.[0-9]{2}\.[0-9]{2}/", "", $ordnername) == "") {
					return $day . "." . $month . "." . $year;
				} else {
					if (substr(preg_replace("/[0-9]{2}\.[0-9]{2}\.[0-9]{2}/", "", $ordnername), 0, 1) == "_") {
						return substr(preg_replace("/[0-9]{2}\.[0-9]{2}\.[0-9]{2}/", "", $ordnername), 1) . "_" . $day . "." . $month . "." . $year;
					} else {
						return substr(preg_replace("/[0-9]{2}\.[0-9]{2}\.[0-9]{2}/", "", $ordnername), 0, -1) . "_" . $day . "." . $month . "." . $year;
					}
				}
			case 3:
				$teile = explode("-", $matches[0][0]);
				$year = $teile[0];
				$month = $teile[1];
				$day = $teile[2];
				if (preg_replace("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", "", $ordnername) == "") {
					return $day . "." . $month . "." . $year;
				} else {
					return substr(preg_replace("/[0-9]{4}-[0-9]{2}-[0-9]{2}/", "", $ordnername), 1) . "_" . $day . "." . $month . "." . $year;
				}

		}
	}
	return $ordnername;
}

function get_thread($ordner) {
	// todo neue DB-Verbidnung aufbauen
	global $db, $bot_boardid, $bot_boardid_hidden, $usenumber, $usetopic, $ersetzen;

	$sql = "SELECT threadid, topic FROM bb1_threads WHERE boardid = " . $bot_boardid . " OR boardid = " . $bot_boardid_hidden . " ORDER BY threadid DESC;";
	$result = $db->query($sql);
	$ordner = trim(strtr(strtolower($ordner), $ersetzen));
	while ($row = $result->fetch_array()) {
		$name = trim(strtr(strtolower($row['topic']), $ersetzen));
		if (parse_dateformats($name) == parse_dateformats($ordner)) {
			$usenumber = $row['threadid'];
			$usetopic = htmlentities($row['topic'], ENT_NOQUOTES | ENT_HTML401, 'ISO-8859-1');
			break;
		}
	}

	if ($usenumber == 0) {
		$usetopic = htmlentities($name, ENT_NOQUOTES | ENT_HTML401, 'ISO-8859-1');
	}
}

function makeindex($pfad) {
	$datei = fopen($pfad . "index.php", "w");
	fwrite($datei, "");
	fclose($datei);
}

function query_first($db, $query_string) {
	$result = $db->query($query_string);
	$returnarray = $result->fetch_array();
	return $returnarray;
}

function RotateJpg($filename = '', $angle = 0) {
	$original = imagecreatefromjpeg($filename);
	$rotated = imagerotate($original, $angle, 0);
	imagejpeg($rotated, $filename);
	imagedestroy($rotated);
}

// MySQL-Config
function execute($ho, $state, $array, $newsess) {
	$log_hoster = $array;
	$ch = curl_init();

	$url = $log_hoster[$ho][0];
	$postdata = $log_hoster[$ho][1];
	$ref = $log_hoster[$ho][2];

	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	curl_setopt($ch, CURLOPT_COOKIESESSION, $newsess);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
	curl_setopt($ch, CURLOPT_REFERER, $ref);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
	$retu = curl_exec($ch);
	sleep(1);
	return $retu;
}

function getsite($zielurl, $postdata = "", $ref = "", $newsess = true) {
	$log_hoster = array(
		array($zielurl, $postdata, $ref),
	);
	$temp = execute(0, true, $log_hoster, $newsess);
	return $temp;
}

function replace_umlaute($string) {
	$umlaute = array(
		"&auml;" => "ä",
		"&ouml;" => "ö",
		"&uuml;" => "ü",
		"&Auml;" => "Ä",
		"&Ouml;" => "Ö",
		"&Uuml;" => "Ü",
	);
	return strtr($string, $umlaute);
}

function add_to_help($text) {
	global $helptext;
	$helptext[] = $text;
}

function call_help() {
	global $helptext;

	natsort($helptext);
	$text = "/help --> Dieses Menü\n";
	foreach ($helptext as $textline) {
		$text .= $textline . "\n";
	}
	post_reply($text);
}

// MySQL-Config
// TB-Tabellen
function mysqli_db_connect() {
	global $mysqli, $chatID, $mysql_server, $mysql_user, $mysql_password, $mysql_db, $admin_name;

	try {
		$mysqli = new mysqli($mysql_server, $mysql_user, $mysql_password, $mysql_db);
	} catch (Exception $e) {
		post_reply("Datenbankfehler! @" . $admin_name);
		exit();
	}

	if ($mysqli->connect_errno) {
		post_reply("Datenbankfehler! @" . $admin_name);
		exit();
	}
	$mysqli->set_charset("utf8");
}

// WBB-Tabellen
function db_connect() {
	global $db, $chatID, $mysql_server, $mysql_user, $mysql_password, $db_db, $admin_name;

	try {
		$db = new mysqli($mysql_server, $mysql_user, $mysql_password, $db_db);
	} catch (Exception $e) {
		post_reply("Datenbankfehler! @" . $admin_name);
		exit();
	}

	if ($db->connect_errno) {
		post_reply("Datenbankfehler! @" . $admin_name);
		exit();
	}
	$db->set_charset("utf8");
}

function post_todotable($addtext = '') {
	global $chatID, $mysqli;

	$sql = "SELECT t.ID AS ID, t.content AS content, t.isintodo AS isintodo, l.username AS username FROM tb_todolist t JOIN tb_lastseen_users l ON t.wishby = l.userid WHERE t.isactive=1 ORDER BY t.ID; --";
	$result = $mysqli->query($sql);
	if ($result->num_rows == 0) {
		post_reply($addtext . "\n" . "Es gibt keine offenen Tickets.");
	} else {
		$replytext = $addtext . "\n\nAktuelle ToDo:\n";
		$id_maxlength = 0;
		$row_tmp = array();
		while ($row = $result->fetch_object()) {
			$id_maxlength = strlen($row->ID) < $id_maxlength ? $id_maxlength : strlen($row->ID);
			$row_tmp[] = $row;
		}

		foreach ($row_tmp as $row) {
			$status = $row->isintodo == 1 ? "[*]" : "[+]";
			$replytext .= $status . " " . str_pad($row->ID, $id_maxlength, "0", STR_PAD_LEFT) . "| " . $row->content . " (von {$row->username})\n";
		}
		post_reply($replytext);
	}
}