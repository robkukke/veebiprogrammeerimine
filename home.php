<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: page2.php");
}
if (isset($_GET["logout"])){
    session_destroy();
    header("Location: page2.php");
}
require_once "page_header.php";
?>
	<h1><?= $_SESSION["user_firstname"] . " " . $_SESSION["user_lastname"] ?>, veebiprogrammeerimine</h1>
	<p>See leht on valminud õppetöö raames ja ei sisalda mingit tõsiseltvõetavat sisu!</p>
	<p>Õppetöö toimub <a href="https://www.tlu.ee/dt">Tallinna Ülikooli Digitehnoloogiate instituudis</a>.</p>
	<p>Õppetöö toimus 2021 sügisel.</p>
	<hr>
	<ul>
		<li><a href="?logout=1">Logi välja</a></li>
		<li><a href="list_films.php">Filmide nimekirja vaatamine</a> versioon 1</li>
		<li><a href="add_films.php">Filmide lisamine andmebaasi</a> versioon 1</li>
		<li><a href="user_profile.php">Kasutajaprofiil</a></li>
		<li><a href="movie_relations.php">Filmi info seoste loomine</a></li>
		<li><a href="add_movie_data.php">Info lisamine filmide andmebaasi</a></li>
		<li><a href="person_in_movie_relations.php">Filmide ja tegelaste seosed</a></li>
	</ul>
</body>
</html>