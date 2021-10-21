<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: page2.php");
}
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: page2.php");
}
require_once "../../config.php";
require_once "fnc_movie.php";
require_once "page_header.php";
$output_html = null;
$output_html = read_all_person_in_movie_relations();
?>
	<h1><?= $_SESSION["user_firstname"] . " " . $_SESSION["user_lastname"] ?>, veebiprogrammeerimine</h1>
	<p>See leht on valminud õppetöö raames ja ei sisalda mingit tõsiseltvõetavat sisu!</p>
	<p>Õppetöö toimub <a href="https://www.tlu.ee/dt">Tallinna Ülikooli Digitehnoloogiate instituudis</a>.</p>
	<p>Õppetöö toimus 2021 sügisel.</p>
	<hr>
	<ul>
		<li><a href="?logout=1">Logi välja</a></li>
		<li><a href="home.php">Avaleht</a></li>
	</ul>
	<hr>
	<h2>Filmide ja tegelaste seosed</h2>
	<?= $output_html ?>
</body>
</html>