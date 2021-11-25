<?php
$database = "if21_robin_ku";
require_once "fnc_general.php";

function store_news($title, $content, $expire, $photoid = null) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "INSERT INTO vpr_news (userid, title, content, expire, photoid) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "isssi",
        $_SESSION["user_id"],
        $title,
        $content,
        $expire,
        $photoid
    );
    if ($stmt->execute()) {
        $notice = "Uus uudis edukalt salvestatud!";
    } else {
        $notice =
            "Uue uudise andmebaasi salvestamisel tekkis viga: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function display_news() {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT vpr_news.userid, title, content, expire, vpr_news.added, photoid, firstname, lastname, filename FROM vpr_news JOIN vpr_users ON vpr_news.userid = vpr_users.id LEFT JOIN vpr_newsphotos ON vpr_news.photoid = vpr_newsphotos.id WHERE expire > CURDATE()"
    );
    echo $conn->error;
    $stmt->bind_result(
        $userid_from_db,
        $title_from_db,
        $content_from_db,
        $expire_from_db,
        $added_from_db,
        $photoid_from_db,
        $firstname_from_db,
        $lastname_from_db,
        $filename_from_db
    );
    $news_html = null;
    $photo_upload_normal_dir = "upload_photos_normal/";
    $stmt->execute();
    while ($stmt->fetch()) {
        $news_html .= "\n <h3>" . $title_from_db . "</h3> \n";
        $news_html .= "<p>" . $firstname_from_db . " " . $lastname_from_db . "<br> \n";
        $news_html .= "Lisatud: " . date_to_est_format($added_from_db) . "</p> \n";
        $news_html .= "<p>" . htmlspecialchars_decode($content_from_db) . "</p> \n";
        if (!empty($filename_from_db)) {
            $news_html .= '<img alt="Üleslaetud foto" src="' . $photo_upload_normal_dir . $filename_from_db . '">';
        }
        $news_html .= "<br>";
    }
    $stmt->close();
    $conn->close();
    return $news_html;
}
