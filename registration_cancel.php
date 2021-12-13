<?php
require_once "../../config.php";
require_once "fnc_party.php";
$notice = null;
$code = null;
// muutujad võimalike veateadetega
$code_error = null;

// kontrollime sisestust
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["registration_submit"])) {
        if (isset($_POST["code_input"]) and !empty($_POST["code_input"])) {
            $code = filter_var($_POST["code_input"], FILTER_VALIDATE_INT);
        } else {
            $code_error = "Palun sisesta oma üliõpilaskood!";
        }
        // kui vigu pole, siis salvestame
        if (empty($code_error)) {
            $notice = cancel_registration($code);
        }
    } // if (isset) lõppeb
} // id request_method lõppeb
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Veebiprogrammeerimine</title>
</head>
<body>
	<h1>Veebiprogrammeerimine</h1>
	<p>See leht on valminud õppetöö raames ja ei sisalda mingit tõsiseltvõetavat sisu!</p>
	<p>Õppetöö toimub <a href="https://www.tlu.ee/dt">Tallinna Ülikooli Digitehnoloogiate instituudis</a>.</p>
	<p>Õppetöö toimus 2021 sügisel.</p>
	<hr>
	<h2>Tühista oma peole registreerimine</h2>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<label for="code_input">üliõpilaskood:</label><br>
		<input id="code_input" name="code_input" type="text" value="<?= $code ?>"><span><?= $code_error ?></span><br>
		<input name="registration_submit" type="submit" value="Tühista"><span><?= $notice ?></span>
	</form>
	<hr>
</body>
</html>