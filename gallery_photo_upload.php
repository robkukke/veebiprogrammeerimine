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
require_once "fnc_photoupload.php";
$photo_error = null;
$photo_upload_notice = null;
$photo_upload_orig_dir = "upload_photos_orig/";
$photo_upload_normal_dir = "upload_photos_normal/";
$photo_upload_thumb_dir = "upload_photos_thumb/";
$normal_photo_max_width = 600;
$normal_photo_max_height = 400;
$thumbnail_width = $thumbnail_height = 100;
$my_temp_image = null;
$watermark_file = "pics/vp_logo_w100_overlay.png";
$file_type = null;
$file_name = null;
$alt_text = null;
$privacy = 1;
$photo_filename_prefix = "vp_";
$photo_upload_size_limit = 1024 * 1024;
$photo_size_ratio = 1;

if (isset($_POST["photo_submit"])) {
    if (isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])) {
        // kas on pilt ja mis tüüpi?
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
            // var_dump($image_check);
        } else {
            $photo_error = "Valitud fail ei ole pilt!";
        }
        // kas on lubatud suurusega
        if (empty($photo_error) and $_FILES["photo_input"]["size"] > $photo_upload_size_limit) {
            $photo_error .= "Valitud fail on liiga suur!";
        }
        // kas alt tekst on
        if (isset($_POST["alt_input"]) and !empty($_POST["alt_input"])) {
            $alt_text = test_input(filter_var($_POST["alt_input"], FILTER_SANITIZE_STRING));
            if (empty($alt_text)) {
                $photo_error .= "Alternatiivtekst on lisamata!";
            }
        }
        // kas on privaatsus
        if (isset($_POST["privacy_input"]) and !empty($_POST["privacy_input"])) {
            $privacy = filter_var($_POST["privacy_input"], FILTER_VALIDATE_INT);
        }
        if (empty($privacy)) {
            $photo_error .= "Privaatsus on määramata!";
        }
        if (empty($photo_error)) {
            // teen ajatempli
            $time_stamp = microtime(1) * 10000;
            // moodustan failinime, kasutame eesliidet
            $file_name = $photo_filename_prefix . $time_stamp . "." . $file_type;
            // teen graafikaobjekti, image objekti
            if ($file_type == "jpg") {
                $my_temp_image = imagecreatefromjpeg($_FILES["photo_input"]["tmp_name"]);
            }
            if ($file_type == "png") {
                $my_temp_image = imagecreatefrompng($_FILES["photo_input"]["tmp_name"]);
            }
            if ($file_type == "gif") {
                $my_temp_image = imagecreatefromgif($_FILES["photo_input"]["tmp_name"]);
            }
            // loome uue pikslikogumi
            $my_new_temp_image = resize_photo($my_temp_image, $normal_photo_max_width, $normal_photo_max_height);
            // lisan vesimärgi
            $my_new_temp_image = add_watermark($my_new_temp_image, $watermark_file);
            // salvestan
            $photo_upload_notice = "Vähendatud pildi " . save_image($my_new_temp_image, $file_type, $photo_upload_normal_dir . $file_name);
            imagedestroy($my_new_temp_image);
            // teen pisipildi
            $my_new_temp_image = resize_photo($my_temp_image, $thumbnail_width, $thumbnail_height, false);
            $photo_upload_notice .= " Pisipildi " . save_image($my_new_temp_image, $file_type, $photo_upload_thumb_dir . $file_name);
            imagedestroy($my_new_temp_image);
            imagedestroy($my_temp_image);
            // kopeerime pildi originaalkujul, originaalnimega vajalikku kataloogi
            if (move_uploaded_file($_FILES["photo_input"]["tmp_name"], $photo_upload_orig_dir . $file_name)) {
                $photo_upload_notice .= " Originaalfoto laeti üles!";
            } else {
                $photo_upload_notice .= " Foto üleslaadimine ei õnnestunud!";
            }
            $photo_upload_notice .= " " . store_photo_data($file_name, $alt_text, $privacy);
            $alt_text = null;
            $privacy = 1;
        }
    } else {
        $photo_error = "Pildifaili pole valitud!";
    }
    if (empty($photo_upload_notice)) {
        $photo_upload_notice = $photo_error;
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
	<h2>Fotode üleslaadimine</h2>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype="multipart/form-data" method="post">
		<label for="photo_input">Vali pildifail </label>
		<input id="photo_input" name="photo_input" type="file">
		<br>
		<label for="alt_input">Alternatiivtekst (alt): </label>
		<input id="alt_input" name="alt_input" placeholder="pildi alternatiivtekst" type="text" value="<?= $alt_text ?>">
		<br>
		<input id="privacy_input_1" name="privacy_input" type="radio" value="1" <?php if ($privacy == 1) {echo " checked";} ?>>
		<label for="privacy_input_1">Privaatne (ainult mina näen)</label>
		<br>
		<input id="privacy_input_2" name="privacy_input" type="radio" value="2" <?php if ($privacy == 2) {echo " checked";} ?>>
		<label for="privacy_input_2">Sisseloginud kasutajatele</label>
		<br>
		<input id="privacy_input_3" name="privacy_input" type="radio" value="3" <?php if ($privacy == 3) {echo " checked";} ?>>
		<label for="privacy_input_3">Avalik (kõik näevad)</label>
		<br>
		<input name="photo_submit" type="submit" value="Lae pilt üles">
	</form>
	<p><?= $photo_upload_notice ?></p>
</body>
</html>