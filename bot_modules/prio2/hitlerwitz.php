<?php

add_to_help("/hitlerwitz --> Tells the infamous Hitlerwitz!");

if ($glob_switcher == '/hitlerwitz') {
	$text = "Hitler marschiert in Polen ein.\n";
	$text .= "Er läuft mittig vor zwei Solden. Einer rechts und einer links an seiner Seite.\n";
	$text .= "Grimmig schauen, betritt er ein Dorf. Er schreitet langsam voran, einen Fuß vor den anderen setzend.\n";
	$text .= "\nNach kurzer Zeit kommt er an einer Schmiede vorbei. Der schmied sieht Hitler, erstarrt und lässt vor Schreck seinen Hammer fallen.\n";
	$text .= "Dann kommt er an einer Mühle vorbei. Der Müller schaut ihn an, lässt vor Schreck seinen Sack mit Mehl fallen, den er gerade in der Hand hielt.\n";
	$text .= "Unterdessen schreitet Hitler ohne eine Miene zu verziehen voran.\n\n";
	$text .= "Weiter in dem Dorf kommt er an einer Näherei vorbei. Die Schneiderinnen sehen Hitler und blickten ihn wie erstarrt an, so angsterfüllt sind sie.\n";
	$text .= "Dabenen ist ein Obstverkäufer. Auch er sieht den Führer und kann es nicht fassen. Er bringt kein Wort heraus, so erstarrt ist er.\n\n\n";
	$text .= "Ein ganzes Stück später befindet sich eine große Wiese, auf der ein kleines Mädchen spielt. Es turnt umher und pflückt dabei Blumen. So bunt, wie man sie sich nur vorstellen kann und von solch einer Schönheit, wie es sie sonst nirgens auf der Welt gibt!\n";
	$text .= "Als der Führer das sieht, zitiert er das Mädchen sofort zu sich heran und fragt sie: 'Na meine Kleine, was pflückst du denn da Schönes?'.\n";
	$text .= "Das Mädchen antwortet: 'Mein Führer, das sind die buntesten und schönsten Blumen, die man hier in der Gegend finden kann! Nie wieder wirst du so etwas schönes sehen!'.\n";
	$text .= "Sichtlich erfreut fragt er da Mädchen, ob er denn nicht einmal daran riechen dürfte. Das Mädchen streckt ihm den Strauß Blumen hin und Hitler hält seinen Kolben hinein.\n\n";
	$text .= "Da plötzlich holt das Mädchen aus und steckt ihm ein Bündel Gras in den Mund.\n";
	$text .= "Irritiert schnauzt Hitler das Kind an: 'Meine Fresse, was soll das? Ich werde dich standrechtlich erschießen lassen!'.\n";
	$text .= "Da sagt das Kind: 'Mein Vater hat gesagt, wenn der Führer ins Gras beißt, wird alles besser!'";
	post_reply($text);

	send_sticker("BQADAgADRAIAAiHtuwM4RQnJhcQXrwI");
}
$hasbeentriggered = true;
