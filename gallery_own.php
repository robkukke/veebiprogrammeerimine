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
require_once "fnc_gallery.php";
$photo_upload_orig_dir = "upload_photos_orig/";
$photo_upload_normal_dir = "upload_photos_normal/";
$photo_upload_thumb_dir = "upload_photos_thumb/";
$page = 1;
$limit = 5;
$photo_count = count_own_photos();
$to_head = '<link rel="stylesheet" type="text/css" href="style/gallery.css">';

if (!isset($_GET["page"]) or $_GET["page"] < 1) {
    $page = 1;
} elseif (round($_GET["page"] - 1) * $limit >= $photo_count) {
    $page = ceil($photo_count / $limit);
} else {
    $page = $_GET["page"];
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
	<h2>Minu oma fotode galerii</h2>
	<div>
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
		<?= read_own_photo_thumbs($page, $limit) ?>
	</div>
</body>
</html>