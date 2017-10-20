<?php

// Post to Telegram
include "./config.php";
include "./functions.php";

$chatID = $randompic_chatID;
if (isset($post_interval_text) && trim($post_interval_text) != '') {
	post_reply($post_interval_text);
}

if (isset($post_interval_video_id) && trim($post_interval_video_id) != '' && isset($post_interval_sticker_id) && trim($post_interval_sticker_id) != '') {
	if (rand(0, 1) == 0) {
		send_document($post_interval_video_id);
	}else{
		send_sticker($post_interval_sticker_id);
	}
} else {
	if (isset($post_interval_video_id) && trim($post_interval_video_id) != '') {
		send_document($post_interval_video_id);
	}

	if (isset($post_interval_sticker_id) && trim($post_interval_sticker_id) != '') {
		send_sticker($post_interval_sticker_id);
	}
}