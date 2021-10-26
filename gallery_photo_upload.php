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
require_once "page_header.php";

$photo_upload_notice = null;
$my_temp_image = null;
$photo_upload_orig_dir = "upload_photos_orig/";
$photo_upload_normal_dir = "upload_photos_normal/";
$photo_upload_thumb_dir = "upload_photos_thumb/";
$file_name_prefix = "vp_";
$file_size_limit = 1024 * 1024;
$photo_max_width = 600;
$photo_max_height = 400;
$photo_size_ratio = 1;
$watermark_file = "pics/vp_logo_color_w100_overlay.png";
$file_type = null;
$file_name = null;
$alt_text = null;
$privacy = 1;

if (isset($_POST["photo_submit"])) {
    // var_dump($_FILES);
    if (isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])) {
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
        } else {
            $photo_upload_notice = "Valitud fail ei ole pilt!";
        }
        // kas on lubatud suurusega (mahuga)
        if (empty($photo_upload_notice) and $_FILES["photo_input"]["size"] > $file_size_limit) {
            $photo_upload_notice = "Valitud fail on liiga suur!";
        }
        if (empty($photo_upload_notice)) {
            // teen ajatempli
            $time_stamp = microtime(1) * 10000;
            // moodustan failinime
            $file_name = $file_name_prefix . $time_stamp . "." . $file_type;
            /**
             * hakkan pildi suurust ka muutma
             * loon image objekti
             */
            if ($file_type == "jpg") {
                $my_temp_image = imagecreatefromjpeg($_FILES["photo_input"]["tmp_name"]);
            }
            if ($file_type == "png") {
                $my_temp_image = imagecreatefrompng($_FILES["photo_input"]["tmp_name"]);
            }
            if ($file_type == "gif") {
                $my_temp_image = imagecreatefromgif($_FILES["photo_input"]["tmp_name"]);
            }
            // foto originaalmõõdud
            $image_width = imagesx($my_temp_image);
            $image_height = imagesy($my_temp_image);
            if ($image_width / $photo_max_width > $image_height / $photo_max_height) {
                $photo_size_ratio = $image_width / $photo_max_width;
            } else {
                $photo_size_ratio = $image_height / $photo_max_height;
            }
            // uued mõõdud
            $image_new_width = round($image_width / $photo_size_ratio);
            $image_new_height = round($image_height / $photo_size_ratio);
            // loon uute mõõtudega image objekti
            $my_new_temp_image = imagecreatetruecolor($image_new_width, $image_new_height);
            // kopeerime vajalikud pikslid suurelt kujutiselt väiksele
            imagecopyresampled(
                $my_new_temp_image,
                $my_temp_image,
                0,
                0,
                0,
                0,
                $image_new_width,
                $image_new_height,
                $image_width,
                $image_height
            );
            // lisan vesimärgi
            $watermark = imagecreatefrompng($watermark_file);
            $watermark_width = imagesx($watermark);
            $watermark_height = imagesy($watermark);
            $watermark_x = $image_new_width - $watermark_width - 10;
            $watermark_y = $image_new_height - $watermark_height - 10;
            imagecopy(
                $my_new_temp_image,
                $watermark,
                $watermark_x,
                $watermark_y,
                0,
                0,
                $watermark_width,
                $watermark_height
            );
            imagedestroy($watermark);
            // salvestamine
            $photo_upload_notice = save_image(
                $my_new_temp_image,
                $file_type,
                $photo_upload_normal_dir . $file_name
            );
            imagedestroy($my_new_temp_image);
            // võin teha veel mingi suurusega variandi
            imagedestroy($my_temp_image);
            move_uploaded_file($_FILES["photo_input"]["tmp_name"], $photo_upload_orig_dir . $file_name);
        }
    } else {
        $photo_upload_notice = "Pilifaili pole valitud!";
    }
} // kui submit klikiti
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
		<label for="photo_input">Vali foto fail</label>
		<input id="photo_input" name="photo_input" type="file">
		<br>
		<label for="alt_input">Alternatiivtekst (alt): </label>
		<input id="alt_input" name="alt_input" placeholder="pildi alternatiivtekst" type="text" value="<?= $alt_text ?>">
		<br>
		<input id="privacy_input_1" name="privacy_input" type="radio" value="1" <?php if ($privacy == 1) {echo " checked";} ?>>
		<label for="privacy_input_1">Privaatne (ainult mina näen)</label>
		<br>
		<input id="privacy_input_2" name="privacy_input" type="radio" value="2" <?php if ($privacy == 2) {echo " checked";} ?>>
		<label for="privacy_input_2">Sisseloginud kasutajale</label>
		<br>
		<input id="privacy_input_3" name="privacy_input" type="radio" value="3" <?php if ($privacy == 3) {echo " checked";} ?>>
		<label for="privacy_input_3">Avalik, kõik näevad</label>
		<br>
		<input name="photo_submit" type="submit" value="Lae pilt üles">
	</form>
	<p><?= $photo_upload_notice ?></p>
</body>
</html>