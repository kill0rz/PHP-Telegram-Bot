<?php

/*
This is the famous excuser by famous SK! @basti_sk
 */

if ($glob_switcher == 'excuse') {
	if (isset($befehle[1])) {
		switch (strtolower($befehle[1])) {
			case 'me':
				post_reply("You may be excused!");
				break;

			// case '1337':
			// 	for ($i = 0; $i < 50; $i++) {
			// 		post_reply(rand(1, 1000));
			// 	}
			// 	break;

			default:
				$text = '';
				if (preg_match("/[^a-zA-Z]/", substr($befehle[1], -1, 1))) {
					if (strtolower(substr($befehle[1], 0, strlen($befehle[1]) - 1)) == "me") {
						post_reply("You may be excused!");
					} else {
						post_reply(ucfirst(substr($befehle[1], 0, strlen($befehle[1]) - 1) . " may be excused!"));
					}
				} else {
					for ($i = 1; $i < count($befehle); $i++) {
						if ($i == 1) {
							$text .= ucfirst($befehle[$i]) . " ";
						} else {
							$text .= $befehle[$i] . " ";
						}
					}
					post_reply($text . "may be excused!");
				}
		}
	}
}
$hasbeentriggered = true;
