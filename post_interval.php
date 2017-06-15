<?php

// Post to Telegram
include "./config.php";
include "./functions.php";

$chatID = $randompic_chatID;
if (isset($post_interval_text) && trim($post_interval_text) != '') {
	post_reply($post_interval_text);
}

if (isset($post_interval_video_id) && trim($post_interval_video_id) != '') {
	send_document($post_interval_video_id);
}