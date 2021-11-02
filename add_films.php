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
require_once "fnc_general.php";
// echo $server_host;
$film_store_notice = null;
$title_input = null;
$year_input = date("Y");
$duration_input = 60;
$genre_input = null;
$studio_input = null;
$director_input = null;
$title_input_error = null;
$year_input_error = null;
$duration_input_error = null;
$genre_input_error = null;
$studio_input_error = null;
$director_input_error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // kas klikiti submit nuppu
    if (isset($_POST["film_submit"])) {
        // kontrollin, et andmeid ikka sisestati
        if (!empty($_POST["title_input"])) {
            $title_input = test_input(filter_var($_POST["title_input"], FILTER_SANITIZE_STRING));
        } else {
            $title_input_error = "Palun sisesta filmi pealkiri!";
        }
        if (!empty($_POST["year_input"])) {
            $year_input = filter_var($_POST["year_input"], FILTER_VALIDATE_INT);
        } else {
            $year_input_error = "Palun sisesta filmi valmimisaasta!";
        }
        if (!empty($_POST["duration_input"])) {
            $duration_input = filter_var($_POST["duration_input"], FILTER_VALIDATE_INT);
        } else {
            $duration_input_error = "Palun sisesta filmi kestus!";
        }
        if (!empty($_POST["genre_input"])) {
            $genre_input = test_input(filter_var($_POST["genre_input"], FILTER_SANITIZE_STRING));
        } else {
            $genre_input_error = "Palun sisesta filmi žanr!";
        }
        if (!empty($_POST["studio_input"])) {
            $studio_input = test_input(filter_var($_POST["studio_input"], FILTER_SANITIZE_STRING));
        } else {
            $studio_input_error = "Palun sisesta filmi tootja!";
        }
        if (!empty($_POST["director_input"])) {
            $director_input = test_input(filter_var($_POST["director_input"], FILTER_SANITIZE_STRING));
        } else {
            $director_input_error = "Palun sisesta filmi lavastaja!";
        }
        if (
            empty($title_input_error) and
            empty($year_input_error) and
            empty($duration_input_error) and
            empty($genre_input_error) and
            empty($studio_input_error) and
            empty($director_input_error)
        ) {
            $film_store_notice = store_film(
                $title_input,
                $year_input,
                $duration_input,
                $genre_input,
                $studio_input,
                $director_input
            );
        } else {
            $film_store_notice = "Osa andmeid on puudu!";
        }
    }
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
		<li><a href="home.php">Avaleht</a></li>
		<li><a href="list_films.php">Filmide nimekirja vaatamine</a> versioon 1</li>
	</ul>
	<hr>
	<h2>Eesti filmid</h2>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<label for="title_input">Filmi pealkiri: </label>
		<input id="title_input" name="title_input" placeholder="filmi pealkiri" type="text" value="<?= $title_input ?>">
		<span><?= $title_input_error ?></span>
		<br>
		<label for="year_input">Valmimisaasta: </label>
		<input id="year_input" min="1912" name="year_input" type="number" value="<?= $year_input ?>">
		<span><?= $year_input_error ?></span>
		<br>
		<label for="duration_input">Kestus minutites: </label>
		<input id="duration_input" max="600" min="1" name="duration_input" type="number" value="<?= $duration_input ?>">
		<span><?= $duration_input_error ?></span>
		<br>
		<label for="genre_input">Filmi žanr: </label>
		<input id="genre_input" name="genre_input" placeholder="žanr" type="text" value="<?= $genre_input ?>">
		<span><?= $genre_input_error ?></span>
		<br>
		<label for="studio_input">Filmi tootja: </label>
		<input id="studio_input" name="studio_input" placeholder="filmi tootja" type="text" value="<?= $studio_input ?>">
		<span><?= $studio_input_error ?></span>
		<br>
		<label for="director_input">Filmi lavastaja: </label>
		<input id="director_input" name="director_input" placeholder="filmi režissöör" type="text" value="<?= $director_input ?>">
		<span><?= $director_input_error ?></span>
		<br>
		<input name="film_submit" type="submit" value="Salvesta">
	</form>
	<p><?= $film_store_notice ?></p>
</body>
</html>