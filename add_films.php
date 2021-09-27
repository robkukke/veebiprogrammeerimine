<?php
require_once "../../config.php";
require_once "fnc_film.php";
// echo $server_host;
$author_name = "Robin Kukke";
$title_input_empty = null;
$year_input_empty = null;
$duration_input_empty = null;
$genre_input_empty = null;
$studio_input_empty = null;
$director_input_empty = null;
// kas klikiti submit nuppu
if (isset($_POST["film_submit"])) {
    $title_input = test_input($_POST["title_input"]);
    $year_input = filter_var(test_input($_POST["year_input"]), FILTER_VALIDATE_INT);
    $duration_input = filter_var(test_input($_POST["duration_input"]), FILTER_VALIDATE_INT);
    $genre_input = test_input($_POST["genre_input"]);
    $studio_input = test_input($_POST["studio_input"]);
    $director_input = test_input($_POST["director_input"]);
    if (
        !empty($title_input) and
        !empty($year_input) and
        !empty($duration_input) and
        !empty($genre_input) and
        !empty($studio_input) and
        !empty($director_input)
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
        if (empty($title_input)) {
            $title_input_empty = "Sisesta filmi pealkiri!";
        }
        if (empty($year_input)) {
            $year_input_empty = "Sisesta valmimisaasta!";
        }
        if (empty($duration_input)) {
            $duration_input_empty = "Sisesta kestus minutites!";
        }
        if (empty($genre_input)) {
            $genre_input_empty = "Sisesta filmi žanr!";
        }
        if (empty($studio_input)) {
            $studio_input_empty = "Sisesta filmi tootja!";
        }
        if (empty($director_input)) {
            $director_input_empty = "Sisesta filmi lavastaja!";
        }
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
	<form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>">
		<label for="title_input">Filmi pealkiri: </label>
		<input id="title_input" name="title_input" placeholder="pealkiri" type="text" value="<?= $title_input ?? "" ?>">
        <?= $title_input_empty ?>
		<br>
		<label for="year_input">Valmimisaasta: </label>
		<input id="year_input" min="1912" name="year_input" type="number" value="<?= $year_input ?? date("Y") ?>">
        <?= $year_input_empty ?>
		<br>
		<label for="duration_input">Kestus minutites: </label>
		<input id="duration_input" min="1" name="duration_input" type="number" value="<?= $duration_input ?? "60" ?>">
        <?= $duration_input_empty ?>
		<br>
		<label for="genre_input">Filmi žanr: </label>
		<input id="genre_input" name="genre_input" placeholder="žanr" type="text" value="<?= $genre_input ?? "" ?>">
        <?= $genre_input_empty ?>
		<br>
		<label for="studio_input">Filmi tootja: </label>
		<input id="studio_input" name="studio_input" placeholder="tootja" type="text" value="<?= $studio_input ?? "" ?>">
        <?= $studio_input_empty ?>
		<br>
		<label for="director_input">Filmi lavastaja: </label>
		<input id="director_input" name="director_input" placeholder="lavastaja" type="text" value="<?= $director_input ?? "" ?>">
        <?= $director_input_empty ?>
		<br>
		<input name="film_submit" type="submit" value="Salvesta">
	</form>
</body>
</html>