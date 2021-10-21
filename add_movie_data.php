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
require_once "fnc_general.php";
require_once "fnc_movie.php";
require_once "page_header.php";
$movie_title = null;
$movie_production_year = null;
$movie_duration = null;
$movie_description = null;
$movie_notice = null;

if (isset($_POST["movie_submit"])) {
    if (isset($_POST["movie_title"]) and !empty($_POST["movie_title"])) {
        $movie_title = test_input(filter_var($_POST["movie_title"], FILTER_SANITIZE_STRING));
    }
    if (empty($movie_title)) {
        $movie_notice .= "Filmi nimi on kirja panemata! ";
    }
    if (isset($_POST["movie_production_year"]) and !empty($_POST["movie_production_year"])) {
        $movie_production_year = test_input(filter_var($_POST["movie_production_year"], FILTER_VALIDATE_INT));
    }
    if (empty($movie_production_year)) {
        $movie_notice .= "Filmi valmimisaasta on kirja panemata! ";
    }
    if (isset($_POST["movie_duration"]) and !empty($_POST["movie_duration"])) {
        $movie_duration = test_input(filter_var($_POST["movie_duration"], FILTER_VALIDATE_INT));
    }
    if (empty($movie_duration)) {
        $movie_notice .= "Filmi kestus on kirja panemata! ";
    }
    if (isset($_POST["movie_description"]) and !empty($_POST["movie_description"])) {
        $movie_description = test_input(filter_var($_POST["movie_description"], FILTER_SANITIZE_STRING));
    }
    if (empty($movie_description)) {
        $movie_notice .= "Filmi kirjeldus on kirja panemata!";
    }
    if (empty($movie_notice)) {
        $movie_notice = store_movie(
            $movie_title,
            $movie_production_year,
            $movie_duration,
            $movie_description
        );
    }
}

$person_first_name = null;
$person_last_name = null;
$person_birth_date = null;
$person_notice = null;

if (isset($_POST["person_submit"])) {
    if (isset($_POST["person_first_name"]) and !empty($_POST["person_first_name"])) {
        $person_first_name = test_input(filter_var($_POST["person_first_name"], FILTER_SANITIZE_STRING));
    }
    if (empty($person_first_name)) {
        $person_notice .= "Näitleja eesnimi on kirja panemata! ";
    }
    if (isset($_POST["person_last_name"]) and !empty($_POST["person_last_name"])) {
        $person_last_name = test_input(
            filter_var($_POST["person_last_name"], FILTER_SANITIZE_STRING)
        );
    }
    if (empty($person_last_name)) {
        $person_notice .= "Näitleja perekonnanimi on kirja panemata! ";
    }
    if (isset($_POST["person_birth_date"]) and !empty($_POST["person_birth_date"])) {
        $person_birth_date = test_input(filter_var($_POST["person_birth_date"], FILTER_SANITIZE_STRING));
    }
    if (empty($person_birth_date)) {
        $person_notice .= "Näitleja sünnipäev on kirja panemata!";
    }
    if (empty($person_notice)) {
        $person_notice = store_person(
            $person_first_name,
            $person_last_name,
            $person_birth_date
        );
    }
}

$genre_name = null;
$genre_description = null;
$genre_notice = null;

if (isset($_POST["genre_submit"])) {
    if (isset($_POST["genre_name"]) and !empty($_POST["genre_name"])) {
        $genre_name = test_input(filter_var($_POST["genre_name"], FILTER_SANITIZE_STRING));
    }
    if (empty($genre_name)) {
        $genre_notice .= "Žanri nimi on kirja panemata! ";
    }
    if (isset($_POST["genre_description"]) and !empty($_POST["genre_description"])) {
        $genre_description = test_input(filter_var($_POST["genre_description"], FILTER_SANITIZE_STRING));
    }
    if (empty($genre_description)) {
        $genre_notice .= "Žanri kirjeldus on kirja panemata!";
    }
    if (empty($genre_notice)) {
        $genre_notice = store_genre($genre_name, $genre_description);
    }
}

$quote_text = null;
$quote_person_in_movie_id = null;
$quote_notice = null;

if (isset($_POST["quote_submit"])) {
    if (isset($_POST["quote_text"]) and !empty($_POST["quote_text"])) {
        $quote_text = test_input(filter_var($_POST["quote_text"], FILTER_SANITIZE_STRING));
    }
    if (empty($quote_text)) {
        $quote_notice .= "Tsitaat on kirja panemata! ";
    }
    if (isset($_POST["quote_person_in_movie_id"]) and !empty($_POST["quote_person_in_movie_id"])) {
        $quote_person_in_movie_id = test_input(filter_var($_POST["quote_person_in_movie_id"], FILTER_VALIDATE_INT));
    }
    if (empty($quote_person_in_movie_id)) {
        $quote_notice .= "Tsitaadi ütleja roll on valimata!";
    }
    if (empty($quote_notice)) {
        $quote_notice = store_quote($quote_text, $quote_person_in_movie_id);
    }
}

$position_name = null;
$position_description = null;
$position_notice = null;

if (isset($_POST["position_submit"])) {
    if (isset($_POST["position_name"]) and !empty($_POST["position_name"])) {
        $position_name = test_input(filter_var($_POST["position_name"], FILTER_SANITIZE_STRING));
    }
    if (empty($position_name)) {
        $position_notice .= "Amet on kirja panemata! ";
    }
    if (isset($_POST["position_description"]) and !empty($_POST["position_description"])) {
        $position_description = test_input(filter_var($_POST["position_description"], FILTER_SANITIZE_STRING));
    }
    if (empty($position_description)) {
        $position_notice .= "Ameti kirjeldus on kirja panemata!";
    }
    if (empty($quote_notice)) {
        $position_notice = store_position(
            $position_name,
            $position_description
        );
    }
}

$production_company_name = null;
$production_company_address = null;
$production_company_notice = null;

if (isset($_POST["production_company_submit"])) {
    if (isset($_POST["production_company_name"]) and !empty($_POST["production_company_name"])) {
        $production_company_name = test_input(filter_var($_POST["production_company_name"], FILTER_SANITIZE_STRING));
    }
    if (empty($production_company_name)) {
        $production_company_notice .= "Tootja nimi on kirja panemata! ";
    }
    if (isset($_POST["production_company_address"]) and !empty($_POST["production_company_address"])) {
        $production_company_address = test_input(filter_var($_POST["production_company_address"], FILTER_SANITIZE_STRING));
    }
    if (empty($production_company_address)) {
        $production_company_notice .= "Tootja aadress on kirja panemata!";
    }
    if (empty($production_company_notice)) {
        $production_company_notice = store_production_company(
            $production_company_name,
            $production_company_address
        );
    }
}
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
	<h2>Info lisamine filmide andmebaasi</h2>
	<h3>Film</h3>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<label for="movie_title">Pealkiri: </label>
		<input id="movie_title" name="movie_title" placeholder="filmi nimi" type="text" value="<?= $movie_title ?>">
		<label for="movie_production_year">Valmimisaasta: </label>
		<input id="movie_production_year" name="movie_production_year" placeholder="filmi valmimisaasta" type="text" value="<?= $movie_production_year ?>">
		<label for="movie_duration">Kestus minutites: </label>
		<input id="movie_duration" name="movie_duration" placeholder="filmi kestus minutites" type="text" value="<?= $movie_duration ?>">
		<label for="movie_description">Kirjeldus: </label>
		<input id="movie_description" name="movie_description" placeholder="filmi kirjeldus" type="text" value="<?= $movie_description ?>">
		<input name="movie_submit" type="submit" value="Lisa">
	</form>
	<p><?= $movie_notice ?></p>
	<h3>Näitleja</h3>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<label for="person_first_name">Eesnimi: </label>
		<input id="person_first_name" name="person_first_name" placeholder="näitleja eesnimi" type="text" value="<?= $person_first_name ?>">
		<label for="person_last_name">Perekonnanimi: </label>
		<input id="person_last_name" name="person_last_name" placeholder="näitleja perekonnanimi" type="text" value="<?= $person_last_name ?>">
		<label for="person_birth_date">Sünnipäev: </label>
		<input id="person_birth_date" name="person_birth_date" type="date" value="<?= $person_birth_date ?>">
		<input name="person_submit" type="submit" value="Lisa">
	</form>
	<p><?= $person_notice ?></p>
	<h3>Žanr</h3>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<label for="genre_name">Žanr: </label>
		<input id="genre_name" name="genre_name" placeholder="žanri nimi" type="text" value="<?= $genre_name ?>">
		<label for="genre_description">Kirjeldus: </label>
		<input id="genre_description" name="genre_description" placeholder="žanri kirjeldus" type="text" value="<?= $genre_description ?>">
		<input name="genre_submit" type="submit" value="Lisa">
	</form>
	<p><?= $genre_notice ?></p>
	<h3>Tsitaat</h3>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<label for="quote_text">Tsitaat: </label>
		<input id="quote_text" name="quote_text" placeholder="tsitaadi tekst" type="text" value="<?= $quote_text ?>">
		<label for="quote_person_in_movie_id">Roll: </label>
		<select id="quote_person_in_movie_id" name="quote_person_in_movie_id">
			<option value="" selected disabled>Vali roll</option>
			<?= read_all_person_in_movie_for_option($quote_person_in_movie_id) ?>
		</select>
		<input name="quote_submit" type="submit" value="Lisa">
	</form>
	<p><?= $quote_notice ?></p>
	<h3>Amet</h3>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<label for="position_name">Nimi: </label>
		<input id="position_name" name="position_name" placeholder="ameti nimi" type="text" value="<?= $position_name ?>">
		<label for="position_description">Kirjeldus: </label>
		<input id="position_description" name="position_description" placeholder="ameti kirjeldus" type="text" value="<?= $position_description ?>">
		<input name="position_submit" type="submit" value="Lisa">
	</form>
	<p><?= $position_notice ?></p>
	<h3>Tootja</h3>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<label for="production_company_name">Nimi: </label>
		<input id="production_company_name" name="production_company_name" placeholder="tootja nimi" type="text" value="<?= $production_company_name ?>">
		<label for="production_company_address">Aadress: </label>
		<input id="production_company_address" name="production_company_address" placeholder="tootja aadress" type="text" value="<?= $production_company_address ?>">
		<input name="production_company_submit" type="submit" value="Lisa">
	</form>
	<p><?= $production_company_notice ?></p>
</body>
</html>