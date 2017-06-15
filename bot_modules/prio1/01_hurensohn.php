<?php

// Hurensohn-Filter
if (isset($update["message"]["text"]) && str_replace("hurensohn", "", strtolower($update["message"]["text"])) != strtolower($update["message"]["text"])) {
	if ($update["message"]["from"]["id"] == $admin_id) {
		$text = "Du wolltest nicht mehr so oft Hurensohn sagen!";
	} else {
		$text = "Du sollst nicht Hurensohn sagen!";
	}
	post_reply($text);
}