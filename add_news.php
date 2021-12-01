<?php
require_once "use_session.php";
require_once "../../config.php";
require_once "fnc_news.php";
require_once "fnc_general.php";
require_once "classes/Photoupload.class.php";
$news_notice = null;
$news_error = null;
$news_title = null;
$news = null;
$expire = new DateTime("now");
$expire->add(new DateInterval("P7D"));
$expire_date = date_format($expire, "Y-m-d");
$photo_file = null;
$normal_photo_max_width = 600;
$normal_photo_max_height = 400;
$thumbnail_width = $thumbnail_height = 100;
$photo_filename_prefix = "vpnews_";
$photo_upload_size_limit = 1024 * 1024;
$photo_upload_news_dir = "upload_photos_news/";
$allowed_photo_types = ["image/jpeg", "image/png"];

if (isset($_POST["news_submit"])) {
    if (empty($_POST["title_input"])) {
        $news_error = "Uudise pealkiri on puudu! ";
    } else {
        $news_title = test_input(filter_var($_POST["title_input"], FILTER_SANITIZE_STRING));
    }
    if (empty($_POST["news_input"])) {
        $news_error .= "Uudise sisu on puudu! ";
    } else {
        $news = test_input(filter_var($_POST["news_input"], FILTER_SANITIZE_STRING));
    }
    if (!empty($_POST["expire_input"])) {
        $expire_date = $_POST["expire_input"];
    } else {
        $news_error .= "Palun vali aegumistähtaeg! ";
    }
    if ($expire_date < date("Y-m-d")) {
        $news_error .= "Aegumistähtaeg on minevikus!";
    }
    if (isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])) {
        $photo_upload = new Photoupload($_FILES["photo_input"]);
        if (empty($photo_upload->error)) {
            $photo_upload->check_allowed_type($allowed_photo_types);
            if (empty($photo_upload->error)) {
                $photo_upload->check_size($photo_upload_size_limit);
                if (empty($photo_upload->error) and empty($news_error)) {
                    $photo_upload->create_filename($photo_filename_prefix);
                    $photo_upload->resize_photo(
                        $normal_photo_max_width,
                        $normal_photo_max_height
                    );
                    $news_notice = "Uudise pildi " . $photo_upload->save_image($photo_upload_news_dir . $photo_upload->file_name);
                    $photo_file .= $photo_upload->file_name;
                }
            }
        }
        $news_error .= $photo_upload->error;
        unset($photo_upload);
    }
    if (empty($news_error)) {
        $news_notice .= save_news(
            $news_title,
            $news,
            $expire_date,
            $photo_file
        );
        $news_title = null;
        $news = null;
        $expire = new DateTime("now");
        $expire->add(new DateInterval("P7D"));
        $expire_date = date_format($expire, "Y-m-d");
    }
}

$to_head = '<script src="javascript/checkFileSize.js" defer></script>' . "\n";
$to_head .= '<script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>';

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
		<label for="title_input">Uudise pealkiri</label>
		<input id="title_input" name="title_input" type="text" value="<?= $news_title ?>">
		<br>
		<label for="news_input">Uudis</label>
		<textarea id="news_input" name="news_input"><?= htmlspecialchars_decode($news) ?></textarea>
		<script>CKEDITOR.replace( 'news_input' );</script>
		<br>
		<label for="expire_input">Viimane kuvamise kuupäev</label>
		<input id="expire_input" name="expire_input" type="date" value="<?= $expire_date ?>">
		<br>
		<label for="photo_input">Vali pildifail! </label>
		<input id="photo_input" name="photo_input" type="file">
		<br>
		<input id="news_submit" name="news_submit" type="submit" value="Salvesta uudis">
		<span id="notice"><?= $news_error ?></span>
	</form>
	<span><?= $news_notice ?></span>
</body>
</html>