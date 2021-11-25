<?php
require_once "use_session.php";
require_once "../../config.php";
require_once "fnc_photoupload.php";
require_once "fnc_news.php";
require_once "fnc_general.php";
require_once "classes/Photoupload.class.php";
$news_notice = null;
$expire = new DateTime("now");
$expire->add(new DateInterval("P7D"));
$expire_date = date_format($expire, "Y-m-d");
$normal_photo_max_width = 600;
$normal_photo_max_height = 400;
$allowed_photo_types = ["image/jpeg", "image/png"];
$photo_filename_prefix = "vpnews_";
$photo_upload_size_limit = 1024 * 1024;
$thumbnail_width = $thumbnail_height = 100;
$news_title = null;
$news_text = null;
$photo_error = null;
$photo_upload_notice = null;
$photo_upload_orig_dir = "upload_photos_orig/";
$photo_upload_normal_dir = "upload_photos_normal/";
$photo_upload_thumb_dir = "upload_photos_thumb/";

if (isset($_POST["news_submit"])) {
    // kas pealkiri on
    if (isset($_POST["news_heading"]) and !empty($_POST["news_heading"])) {
        $news_title = test_input(filter_var($_POST["news_heading"], FILTER_SANITIZE_STRING));
    }
    if (empty($news_title)) {
        $news_notice .= "Pealkiri on sisestamata! ";
    }
    // kas sisu on
    if (isset($_POST["news_input"]) and !empty($_POST["news_input"])) {
        $news_text = test_input(filter_var($_POST["news_input"], FILTER_SANITIZE_STRING));
    }
    if (empty($news_text)) {
        $news_notice .= "Sisu on sisestamata! ";
    }
    // kas kehtivusaeg on
    if (isset($_POST["expire_input"]) and !empty($_POST["expire_input"])) {
        $expire_date = $_POST["expire_input"];
    }
    if (empty($expire_date)) {
        $news_notice .= "Kehtivusaeg on sisestamata!";
    }
    if (empty($news_notice)) {
        if (isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])) {
            // fail on, klass kontrollib kohe, kas on foto
            $photo_upload = new Photoupload($_FILES["photo_input"]);
            if (empty($photo_upload->error)) {
                // kas on lubatud tüüpi
                $photo_error .= $photo_upload->check_alowed_type($allowed_photo_types);
                if (empty($photo_upload->error)) {
                    // kas on lubatud suurusega
                    $photo_error .= $photo_upload->check_size($photo_upload_size_limit);
                    // kui seni vigu pole, laeme üles
                    if (empty($photo_error)) {
                        // failinimi
                        $photo_upload->create_filename($photo_filename_prefix);
                        // normaalmõõdus foto
                        $photo_upload->resize_photo($normal_photo_max_width, $normal_photo_max_height);
                        $photo_upload_notice = $photo_upload->save_image($photo_upload_normal_dir . $photo_upload->file_name);
                        // teen pisipildi
                        $photo_upload->resize_photo($thumbnail_width, $thumbnail_height);
                        $photo_upload_notice = $photo_upload->save_image($photo_upload_thumb_dir . $photo_upload->file_name);
                        // kopeerime pildi originaalkujul, originaalnimega vajalikku kataloogi
                        $photo_upload_notice = $photo_upload->move_original($photo_upload_orig_dir . $photo_upload->file_name);
                        // kirjutame andmetabelisse
                        $photo_upload_notice = store_newsphoto_data($photo_upload->file_name);
                    }
                }
            } else {
                $photo_error .= " " . $photo_upload->error;
            }
            unset($photo_upload);
        }
        $news_notice = store_news(
            $news_title,
            $news_text,
            $expire_date,
            $photo_upload_notice
        );
        $news_title = null;
        $news_text = null;
        $expire = new DateTime("now");
        $expire->add(new DateInterval("P7D"));
        $expire_date = date_format($expire, "Y-m-d");
    }
}

// $to_head = '<script src="javascript/checkFileSize.js" defer></script>' ."\n";
$to_head = '<script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>' . "\n";

require "page_header.php";
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
	<h2>Uudise lisamine</h2>
	<form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype="multipart/form-data" method="post">
		<label for="news_heading">Uudise pealkiri </label>
		<input id="news_heading" name="news_heading" placeholder="uudise pealkiri" size="50" type="text" value="<?= $news_title ?>">
		<br>
		<label for="news_input">Uudise sisu</label>
		<br>
		<textarea id="news_input" name="news_input"><?= $news_text ?></textarea>
		<script>CKEDITOR.replace( 'news_input' );</script>
		<br>
		<label for="expire_input">Uudise kehtivus </label>
		<input id="expire_input" name="expire_input" type="date" value="<?= $expire_date ?>">
		<br>
		<label for="photo_input">Vali pildifail! </label>
		<input id="photo_input" name="photo_input" type="file">
		<br>
		<input id="news_submit" name="news_submit" type="submit" value="Salvesta uudis">
	</form>
	<span><?= $news_notice ?></span>
</body>
</html>