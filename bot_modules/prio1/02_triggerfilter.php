<?php

// Trigger-Filter
if (isset($triggersticker) && count($triggersticker) > 0) {
	foreach ($triggersticker as $triggerword => $triggerstickerid) {
		if (isset($update["message"]["text"]) && preg_match($triggerword, strtolower($update["message"]["text"]))) {
			send_sticker($triggerstickerid);
			exit();
		}
	}
}

if (isset($triggersticker_perperson) && count($triggersticker_perperson) > 0) {
	foreach ($triggersticker_perperson as $triggersticker) {
		if (isset($update["message"]["text"]) && preg_match($triggersticker['searchpattern'], strtolower($update["message"]["text"])) && $update["message"]["from"]["id"] == $triggersticker['userid']) {
			send_sticker($triggersticker['stickerid']);
			exit();
		}
	}
}