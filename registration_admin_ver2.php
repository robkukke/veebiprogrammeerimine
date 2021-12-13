<?php
require_once "../../config.php";
require_once "fnc_general.php";
require_once "fnc_party.php";
$notice = null;

// kontrollime sisestust
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["payment_submit"])) {
        if (!empty($_POST["id_input"])) {
            set_payment($_POST["id_input"]);
        }
    }
}
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
	<h2>Peole on end kirja pannud</h2>
	<?= forms_for_payment() ?>
	<hr>
</body>
</html>