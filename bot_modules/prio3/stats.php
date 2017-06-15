<?php

if (!$hasbeentriggered) {
	// Stats
	$all_words = explode(" ", str_replace("\n", "", $update["message"]["text"]));
	foreach ($all_words as $word) {
		insert_or_update_word($word, $update["message"]["from"]["id"]);
	}
}