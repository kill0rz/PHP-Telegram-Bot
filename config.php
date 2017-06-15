<?php

// Bot-Token von Telegram
define('BOT_TOKEN', 'HIERMUSSDERTOKENREIN');

// Zugangsdaten für die Telegram-Bot-Datenbank | User muss auch auf die DB vom WBB2 Zugriff haben
$mysql_server = "localhost";
$mysql_user = "telegram_bot";
$mysql_password = "telegram_bot";
$mysql_db = "telegram_bot";

// Name der WBB2-Datenbank
$db_db = "wbb2forum";

// Telegram @-Name des Administrators
$admin_name = "admin";
// Telegram ID des Administrators
$admin_id = "123456789";

// WBB2-Daten
// User-ID des Bots
$bot_userid = 1;
// Board-ID der Fotoalben
$bot_boardid = 1;
// Telegram @-Name des Bots
$bot_atname = "@mybot";
// Boardnummer
$n = 1;

// Beispielname für die Hilfe
$example_name = "Username";

// Relativer Pfad zu den Fotoalben auf dem Webspace - OHNE / AM ENDE!
$subordner = "../wbb2/Fotoalben";
// URL zu deinen Fotoalben - OHNE / AM ENDE!
$url2board_fotoalbum = "https://example.com/wbb2/Fotoalben";
// Sollen immer alle Bilder zum Randompic der Woche freigegeben werden? Ja = true; Nein = false
$config_always_allow_randompic = true;

// Zufallsbild der Woche (nur, wenn du es nutzt)
$randompic_chatID = "-123456";

// Worttrigger-Wort: Nach welchem Wort soll gesucht werden?
$triggerword = "wort";
// Worttrigger-Sticker: Mit welchem Sticker soll geantwortet werden? (ID eintragen)
$triggerstricker = "";

// Intervallaufruf Text
$post_interval_text = "";
// Intervallaufruf Video (ID eintragen)
$post_interval_video_id = "";

// URL zu deinem Bot (ohne / am Ende)
$url2bot = "https://board.tld/telegram_bot";

// nicht editieren
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
define('API_URL_FILE', 'https://api.telegram.org/file/bot' . BOT_TOKEN . '/');