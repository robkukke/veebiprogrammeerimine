<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: page2.php");
}
if (isset($_GET["logout"])){
    session_destroy();
    header("Location: page2.php");
}
require_once "../../config.php";
require_once "fnc_film.php";
// echo $server_host;
$film_html = null;
$film_html = read_all_films();
require_once "page_header.php";
?>
	<h1><?= $_SESSION["user_firstname"] . " " . $_SESSION["user_lastname"] ?>, veebiprogrammeerimine</h1>
	<p>See leht on valminud õppetöö raames ja ei sisalda mingit tõsiseltvõetavat sisu!</p>
	<p>Õppetöö toimub <a href="https://www.tlu.ee/dt">Tallinna Ülikooli Digitehnoloogiate instituudis</a>.</p>
	<p>Õppetöö toimus 2021 sügisel.</p>
	<hr>
	<ul>
		<li><a href="?logout=1">Logi välja</a></li>
		<li><a href="home.php">Avaleht</a></li>
		<li><a href="add_films.php">Filmide lisamine andmebaasi</a> versioon 1</li>
	</ul>
	<hr>
	<h2>Eesti filmid</h2>
	<?= $film_html ?>
</body>
</html>