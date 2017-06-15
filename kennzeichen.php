<?php

include 'config.php';
include 'functions.php';

$sitecontent = explode("\n", getsite("https://www.wochenspiegel-web.de/wisl_s-cms/_wochenspiegel/8/Menue/24344/Glueckskennzeichen.html"));

foreach ($sitecontent as $key => $line) {
	if (str_replace('<br><p><em>Gl&uuml;ckskennzeichen</em><br />', '', $line) != $line) {
		$kennzeichen_tmp = preg_match("/<strong>([A-Z]{1,3}(\&nbsp\;)?\s?[A-Z]{1,2}(\&nbsp\;)?\s?[0-9]{1,4})<\/strong>/", $sitecontent[$key + 3], $matches_1);
		$gewinn_tmp = preg_match("/([0-9]{1,5})\sEuro/", $sitecontent[$key + 4], $matches_2);
		if (isset($matches_1[1]) && isset($matches_2[1])) {
			mysqli_db_connect();

			$kennzeichen = trim(str_replace("&nbsp;", " ", $matches_1[1]));
			echo "Kennzeichen: " . $kennzeichen . "\n";
			$gewinn = trim($matches_2[1]);

			$sql = "SELECT lu.*, k.kennzeichen,k.notified,k.id AS kid FROM tb_lastseen_users lu JOIN tb_kennzeichen k ON k.userid = lu.userid WHERE LOWER(k.kennzeichen) = '" . strtolower($kennzeichen) . "';";
			$result = $mysqli->query($sql);

			if (isset($result->num_rows) && $result->num_rows == 1) {
				while ($row = $result->fetch_object()) {
					if ($row->notified == 0) {
						$chatID = $randompic_chatID;
						$text = "Hallo " . $row->username . ",\nHerzlichen Glückwunsch! \xF0\x9F\x98\x81 Dein Kennzeichen " . $kennzeichen . " wurde im Wochenspiegel gezogen! Hol' dir deinen Gewinn von " . $gewinn . "€ ab! \xF0\x9F\x8E\x89 --> https://www.wochenspiegel-web.de/wisl_s-cms/_wochenspiegel/8/Menue/24344/Glueckskennzeichen.html";
						post_reply($text);

						// kennzeichne, dass es schon mal vorkam
						$sql = "UPDATE tb_kennzeichen SET notified=1 WHERE id='" . $row->kid . "'; --";
						$mysqli->query($sql);
					}
				}
			} else {
				$sql = "UPDATE tb_kennzeichen SET notified=0; --";
				$mysqli->query($sql);
			}
		} else {
			echo "error: Konnte Kennzeichen nicht crawlen!\n";
		}
		break;
	}
}
