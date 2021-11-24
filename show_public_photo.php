<?php
$id = null;
$type = "image/png";
$output = "../pics/wrong.png";
$photo_upload_normal_dir = "upload_photos_normal/";
$privacy = 3;

if (isset($_GET["photo"]) and !empty($_GET["photo"])) {
    $id = filter_var($_GET["photo"], FILTER_VALIDATE_INT);
}

if (!empty($id)) {
    require_once "../../config.php";
    $database = "if21_robin_ku";
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT filename from vpr_photos WHERE id = ? AND privacy = ? AND deleted IS NULL"
    );
    echo $conn->error;
    $stmt->bind_param("ii", $id, $privacy);
    $stmt->bind_result($filename_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        $output = $photo_upload_normal_dir . $filename_from_db;
        $check = getimagesize($output);
        $type = $check["mime"];
    }
    $stmt->close();
    $conn->close();
}

header("Content-type: " . $type);
readfile($output);
