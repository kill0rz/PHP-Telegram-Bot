<?php

// Trigger-Filter
foreach ($triggersticker as $triggerword => $triggerstickerid) {
	if (isset($update["message"]["text"]) && preg_replace($triggerword, "", strtolower($update["message"]["text"])) != strtolower($update["message"]["text"])) {
		send_sticker($triggerstickerid);
		exit();
	}
}