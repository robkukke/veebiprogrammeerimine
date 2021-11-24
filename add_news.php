<?php
require_once "use_session.php";
require_once "../../config.php";
require_once "fnc_photoupload.php";
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

if (isset($_POST["news_submit"])) {
    /**
     * Uudise sisu kontrollimiseks kindlasti kasutada meie test_input funktsiooni (fnc_general.php).
     * seal on htmlspecialchars(uudis), mis kodeerib html märgid ringi (  < --> &lt;  )
     * uudise näitamisel on neid tagasi vaja, selleks htmlspecialchars_decode(uudis andmebaasist)
     */
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
		<!--Vaja oleks ka text tüüpi sisendit uudise pealkirjale-->
		<label for="news_input">Uudise sisu</label>
		<br>
		<textarea id="news_input" name="news_input"></textarea>
		<script>CKEDITOR.replace( 'news_input' );</script>
		<br>
		<input id="expire_input" name="expire_input" type="date" value="<?= $expire_date ?>">
		<label for="photo_input"> Vali pildifail! </label>
		<input id="photo_input" name="photo_input" type="file">
		<br>
		<input id="news_submit" name="news_submit" type="submit" value="Salvesta_uudis">
	</form>
	<span><?= $news_notice ?></span>
</body>
</html>