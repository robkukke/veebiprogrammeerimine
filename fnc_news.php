<?php
$database = "if21_robin_ku";

function save_news($news_title, $news, $expire_date, $file_name) {
    $photo_id = null;
    // foto lisamine
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    if (!empty($file_name)) {
        $stmt = $conn->prepare(
            "INSERT INTO vpr_newsphotos (userid, filename) VALUES(?, ?)"
        );
        echo $conn->error;
        $stmt->bind_param("is", $_SESSION["user_id"], $file_name);
        if ($stmt->execute()) {
            $photo_id = $conn->insert_id;
        }
        $stmt->close();
    }
    // uudise lisamine
    $stmt = $conn->prepare(
        "INSERT INTO vpr_news (userid, title, content, photoid, expire) VALUES (?, ?, ?, ?, ?)"
    );
    echo $conn->error;
    $stmt->bind_param(
        "issis",
        $_SESSION["user_id"],
        $news_title,
        $news,
        $photo_id,
        $expire_date
    );
    if ($stmt->execute()) {
        $response = "<br>Uudis on salvestatud!";
    } else {
        $response = "<br>Uudise salvestamine ebaõnnestus!";
    }
    $stmt->close();
    $conn->close();
    return $response;
}

function latest_news($limit) {
    $news_html = null;
    $photo_upload_news_dir = "upload_photos_news/";
    $today = date("Y-m-d");
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT title, content, vpr_news.added, filename FROM vpr_news LEFT JOIN vpr_newsphotos on vpr_newsphotos.id = vpr_news.photoid WHERE vpr_news.expire >= ? AND vpr_news.deleted IS NULL GROUP BY vpr_news.id ORDER By vpr_news.id DESC LIMIT ?"
    );
    echo $conn->error;
    $stmt->bind_param("si", $today, $limit);
    $stmt->bind_result(
        $title_from_db,
        $content_from_db,
        $added_from_db,
        $filename_from_db
    );
    $stmt->execute();
    while ($stmt->fetch()) {
        $news_html .= '<div class="newsblock';
        if (!empty($filename_from_db)) {
            $news_html .= " fullheightnews";
        }
        $news_html .= '">' . "\n";
        if (!empty($filename_from_db)) {
            $news_html .= "\t" . '<img src="' . $photo_upload_news_dir . $filename_from_db . '" ';
            $news_html .= 'alt="' . $title_from_db . '"';
            $news_html .= "> \n";
        }
        $news_html .= "\t <h3>" . $title_from_db . "</h3> \n";
        $addedtime = new DateTime($added_from_db);
        $news_html .= "\t <p>(Lisatud: " . $addedtime->format("d.m.Y H:i:s") . ")</p> \n";
        $news_html .= "\t <div>" . htmlspecialchars_decode($content_from_db) . "</div> \n";
        $news_html .= "</div> \n";
    }
    if ($news_html == null) {
        $news_html = "<p>Kahjuks uudiseid pole!</p>";
    }
    $stmt->close();
    $conn->close();
    return $news_html;
}
