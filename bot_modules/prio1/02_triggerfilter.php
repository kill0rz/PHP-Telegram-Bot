<?php

// Trigger-Filter
foreach ($triggerstricker as $triggerword => $triggerstrickerid) {
	if (isset($update["message"]["text"]) && preg_replace($triggerword, "", strtolower($update["message"]["text"])) != strtolower($update["message"]["text"])) {
		send_sticker($triggerstrickerid);
		exit();
	}
}