<?php
/**
 * moodustame kasutajaprofiiliga seotud värvide jaoks CSS koodi
 * <style>
 * body {
 *	 background-color: #AAAAAA;
 *	 color: #0000AA;
 * }
 * </style>
 */
$css_color = "<style>\n";
$css_color .= "\tbody {\n";
$css_color .= "\t\tbackground-color: " . $_SESSION["bg_color"] . ";\n";
$css_color .= "\t\tcolor: " . $_SESSION["text_color"] . ";\n";
$css_color .= "\t}\n\t</style>\n";
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title><?= $_SESSION["user_firstname"] . " " . $_SESSION["user_lastname"] ?>, veebiprogrammeerimine</title>
	<?= $css_color ?>
</head>
<body>
	<img alt="Veebiprogrammeerimise kursuse bänner" src="pics/vp_banner.png">