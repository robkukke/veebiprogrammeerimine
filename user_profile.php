<?php
require_once "use_session.php";
require_once "../../config.php";
require_once "fnc_user.php";
require_once "fnc_general.php";
$notice = null;
$description = get_user_description();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["profile_submit"])) {
        $description = test_input(filter_var($_POST["description_input"], FILTER_SANITIZE_STRING));
        $notice = save_user_profile($description, $_POST["bg_color_input"], $_POST["text_color_input"]);
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
	</ul>
	<hr>
	<h2>Kasutajaprofiil</h2>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<label for="description_input">Minu lühikirjeldus</label>
		<br>
		<textarea cols="80" id="description_input" name="description_input" placeholder="Minu lühikirjeldus..." rows="10"><?= $description ?></textarea>
		<br>
		<label for="bg_color_input">Taustavärv</label>
		<br>
		<input id="bg_color_input" name="bg_color_input" type="color" value="<?= $_SESSION["bg_color"] ?>">
		<br>
		<label for="text_color_input">Teksti värv</label>
		<br>
		<input id="text_color_input" name="text_color_input" type="color" value="<?= $_SESSION["text_color"] ?>">
		<br>
		<input name="profile_submit" type="submit" value="Salvesta">
	</form>
	<p><?= $notice ?></p>
</body>
</html>