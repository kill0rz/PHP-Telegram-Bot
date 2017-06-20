<?php

// current version
$version = 100;

foreach (glob("update_scripts/*.php") as $filename) {
	if ($filename != "run.php" && (int)str_replace(".php", "", $filename) > $version) {
		include $filename;
	}
}