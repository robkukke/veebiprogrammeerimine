<?php
require_once "use_session.php";
require_once "../../config.php";
require_once "fnc_gallery.php";
require_once "fnc_general.php";
$photo_upload_orig_dir = "upload_photos_orig/";
$photo_upload_normal_dir = "upload_photos_normal/";
$photo_upload_thumb_dir = "upload_photos_thumb/";
$page = 1;
$limit = 15;
$public_from = 2;
$photo_count = count_public_photos($public_from);

if (!isset($_GET["page"]) or $_GET["page"] < 1) {
    $page = 1;
} elseif (round($_GET["page"] - 1) * $limit >= $photo_count) {
    $page = ceil($photo_count / $limit);
} else {
    $page = $_GET["page"];
}

$to_head = '<link rel="stylesheet" type="text/css" href="style/gallery.css">' . "\n";
$to_head .= '<link rel="stylesheet" type="text/css" href="style/modal.css">' . "\n";
$to_head .= '<script src="javascript/modal.js" defer></script>' . "\n";
require_once "page_header.php";
?>
	<!--Modaalaken galeriipildi näitamiseks-->
	<div class="modalarea" id="modalarea">
		<!--sulgemisnupp-->
		<span class="modalclose" id="modalclose">&times;</span>
		<div class="modalhorizontal">
			<div class="modalvertical">
				<p id="modalcaption"></p>
				<img alt="Galeriipilt" id="modalimg" src="pics/empty.png">
				<br>
				<input id="rate1" name="rating" type="radio" value="1"><label for="rate1">1</label>
				<input id="rate2" name="rating" type="radio" value="2"><label for="rate2">2</label>
				<input id="rate3" name="rating" type="radio" value="3"><label for="rate3">3</label>
				<input id="rate4" name="rating" type="radio" value="4"><label for="rate4">4</label>
				<input id="rate5" name="rating" type="radio" value="5"><label for="rate5">5</label>
				<button id="storeRating" type="button">Salvesta hinne</button>
				<br>
				<p id="avgRating"></p>
			</div>
		</div>
	</div>
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
	<h2>Avalike fotode galerii</h2>
	<div class="gallery" id="gallery">
		<p>
			<?php
			// <span>Eelmine leht</span> | <span>Järgmine leht</span>
			if ($page > 1) {
				echo '<span><a href="?page=' . ($page - 1) . '">Eelmine leht</a></span> | ';
			} else {
				echo "<span>Eelmine leht</span> | ";
			}
			if ($page * $limit < $photo_count) {
				echo '<span><a href="?page=' . ($page + 1) . '">Järgmine leht</a></span>';
			} else {
				echo "<span>Järgmine leht</span>";
			}
			?>
		</p>
		<?= read_public_photo_thumbs($public_from, $page, $limit) ?>
	</div>
</body>
</html>