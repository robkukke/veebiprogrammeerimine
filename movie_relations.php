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
$notice = null;
$person_in_movie_notice = null;
$selected_person_for_relation = null;
$selected_movie_for_relation = null;
$selected_position_for_relation = null;
$role = null;

if (isset($_POST["person_in_movie_submit"])) {
    if (isset($_POST["person_select"]) and !empty($_POST["person_select"])) {
        $selected_person_for_relation = filter_var($_POST["person_select"], FILTER_VALIDATE_INT);
    }
    if (empty($selected_person_for_relation)) {
        $person_in_movie_notice .= "Isik on valimata! ";
    }
    if (isset($_POST["movie_select"]) and !empty($_POST["movie_select"])) {
        $selected_movie_for_relation = filter_var($_POST["movie_select"], FILTER_VALIDATE_INT);
    }
    if (empty($selected_movie_for_relation)) {
        $person_in_movie_notice .= "Film on valimata! ";
    }
    if (isset($_POST["position_select"]) and !empty($_POST["position_select"])) {
        $selected_position_for_relation = filter_var($_POST["position_select"], FILTER_VALIDATE_INT);
    }
    if (empty($selected_position_for_relation)) {
        $person_in_movie_notice .= "Amet on valimata! ";
    }
    // kui on näitleja
    if ($selected_position_for_relation == 1) {
        if (isset($_POST["role_input"]) and !empty($_POST["role_input"])) {
            $role = test_input(filter_var($_POST["role_input"], FILTER_SANITIZE_STRING));
        }
        if (empty($role)) {
            $person_in_movie_notice .= "Roll on kirja panemata! ";
        }
    }
    if (empty($person_in_movie_notice)) {
        $person_in_movie_notice = store_person_in_movie(
            $selected_person_for_relation,
            $selected_movie_for_relation,
            $selected_position_for_relation,
            $role
        );
    }
}

$photo_upload_notice = null;
$selected_person_for_photo = null;
$person_photo_dir = "person_photos/";
$file_type = null;
$file_name = null;

if (isset($_POST["person_photo_submit"])) {
    // var_dump($_POST);
    var_dump($_FILES);
    $image_check = getimagesize($_FILES["photo_input"]["tmp_name"]);
    if ($image_check !== false) {
        if ($image_check["mime"] == "image/jpeg") {
            $file_type = "jpg";
        }
        if ($image_check["mime"] == "image/png") {
            $file_type = "png";
        }
        if ($image_check["mime"] == "image/gif") {
            $file_type = "gif";
        }
        // teen ajatempli
        $time_stamp = microtime(1) * 10000;
        // moodustan failinime
        $file_name = "person_" . $_POST["person_select_for_photo"] . "_" . $time_stamp . "." . $file_type;
        move_uploaded_file($_FILES["photo_input"]["tmp_name"], $person_photo_dir . $file_name);
    }
    // move_uploaded_file($_FILES["photo_input"]["tmp_name"], $person_photo_dir . $_FILES["photo_input"]["name"]);
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
	<h2>Filmi info seostamine</h2>
	<h3>Film, inimene ja tema roll</h3>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
		<label for="person_select">Isik: </label>
		<select id="person_select" name="person_select">
			<option value="" selected disabled>Vali isik</option>
			<?= read_all_person_for_option($selected_person_for_relation) ?>
		</select>
		<label for="movie_select">Isik: </label>
		<select id="movie_select" name="movie_select">
			<option value="" selected disabled>Vali film</option>
			<?= read_all_movie_for_option($selected_movie_for_relation) ?>
		</select>
		<label for="position_select">Isik: </label>
		<select id="position_select" name="position_select">
			<option value="" selected disabled>Vali amet</option>
			<?= read_all_position_for_option($selected_position_for_relation) ?>
		</select>
		<label for="role_input">
		<input id="role_input" name="role_input" placeholder="tegelase nimi" type="text" value="<?= $role ?>">
		<input name="person_in_movie_submit" type="submit" value="Salvesta">
	</form>
	<p><?= $person_in_movie_notice ?></p>
	<h3>Filmitegelase foto</h3>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype="multipart/form-data" method="post">
		<label for="person_select_for_photo">Isik: </label>
		<select id="person_select_for_photo" name="person_select_for_photo">
			<option value="" selected disabled>Vali isik</option>
			<?= read_all_person_for_option($selected_person_for_photo) ?>
		</select>
		<label for="photo_input">Vali foto fail</label>
		<input id="photo_input" name="photo_input" type="file">
		<input name="person_photo_submit" type="submit" value="Lae pilt üles">
	</form>
	<p><?= $photo_upload_notice ?></p>
</body>
</html>