<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: page2.php");
}
if (isset($_SESSION["user_firstname"]) and isset($_SESSION["user_lastname"])) {
    $author_name = $_SESSION["user_firstname"] . " " . $_SESSION["user_lastname"];
} else {
    $author_name = "Robin Kukke";
}
if (isset($_GET["logout"])){
    session_destroy();
    header("Location: page2.php");
}
require_once "fnc_user.php";
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title><?= $author_name ?>, veebiprogrammeerimine</title>
</head>
<body>
	<h1><?= $author_name ?>, veebiprogrammeerimine</h1>
	<p>See leht on valminud õppetöö raames ja ei sisalda mingit tõsiseltvõetavat sisu!</p>
	<p>Õppetöö toimub <a href="https://www.tlu.ee/dt">Tallinna Ülikooli Digitehnoloogiate instituudis</a>.</p>
	<p>Õppetöö toimus 2021 sügisel.</p>
	<hr>
	<p>Oled sees!</p>
    <p><a href="?logout=1">Logi välja</a></p>
</body>
</html>