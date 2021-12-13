<?php
require_once "../../config.php";
require_once "fnc_general.php";
require_once "fnc_party.php";
$notice = null;

// kontrollime sisestust
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["payment_submit"])) {
        if (!empty($_POST["person_pay_input"])) {
            set_payment($_POST["person_pay_input"]);
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
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<select name="person_pay_input">
			<?= list_for_payment() ?>
		</select>
		<input name="payment_submit" type="submit" value="Märgi maksnuks">
	</form>
	<hr>
	<h2>Peole seni registreerunud</h2>
	<?= list_registered() ?>
</body>
</html>