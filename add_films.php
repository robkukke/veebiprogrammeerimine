<?php
require_once "../../config.php";
require_once "fnc_film.php";
// echo $server_host;
$author_name = "Robin Kukke";
$film_store_notice = null;
// kas klikiti submit nuppu
if (isset($_POST["film_submit"])) {
    if (
        !empty($_POST["title_input"]) and
        !empty($_POST["genre_input"]) and
        !empty($_POST["studio_input"]) and
        !empty($_POST["director_input"])
    ) {
        $film_store_notice = store_film(
            $_POST["title_input"],
            $_POST["year_input"],
            $_POST["duration_input"],
            $_POST["genre_input"],
            $_POST["studio_input"],
            $_POST["director_input"]
        );
    } else {
        $film_store_notice = "Osa andmeid on puuudu!";
    }
}
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
	<h2>Eesti filmid</h2>
	<form method="post">
		<label for="title_input">Filmi pealkiri: </label>
		<input id="title_input" name="title_input" placeholder="pealkiri" type="text">
		<br>
		<label for="year_input">Valmimisaasta: </label>
		<input id="year_input" min="1912" name="year_input" type="number" value="<?= date("Y") ?>">
		<br>
		<label for="duration_input">Kestus minutites: </label>
		<input id="duration_input" min="1" name="duration_input" type="number" value="60">
		<br>
		<label for="genre_input">Filmi žanr: </label>
		<input id="genre_input" name="genre_input" placeholder="žanr" type="text">
		<br>
		<label for="studio_input">Filmi tootja: </label>
		<input id="studio_input" name="studio_input" placeholder="tootja" type="text">
		<br>
		<label for="director_input">Filmi lavastaja: </label>
		<input id="director_input" name="director_input" placeholder="lavastaja" type="text">
		<br>
		<input name="film_submit" type="submit" value="Salvesta">
	</form>
	<p><?= $film_store_notice ?></p>
</body>
</html>