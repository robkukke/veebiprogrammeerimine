<?php
$database = "if21_robin_ku";

function sign_up(
    $firstname,
    $surname,
    $email,
    $gender,
    $birth_date,
    $password
) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare("SELECT id FROM vpr_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        // kasutaja juba olemas
        $notice = "Sellise tunnusega (" . $email . ") kasutaja on <strong>juba olemas</strong>!";
    } else {
        // sulgen eelmise käsu
        $stmt->close();
        $stmt = $conn->prepare(
            "INSERT INTO vpr_users (firstname, lastname, birthdate, gender, email, password) VALUES(?,?,?,?,?,?)"
        );
        echo $conn->error;
        // krüpteerime salasõna
        $option = ["cost" => 12];
        $pwd_hash = password_hash($password, PASSWORD_BCRYPT, $option);
        $stmt->bind_param(
            "sssiss",
            $firstname,
            $surname,
            $birth_date,
            $gender,
            $email,
            $pwd_hash
        );
        if ($stmt->execute()) {
            $notice = "Uus kasutaja edukalt loodud!";
        } else {
            $notice = "Uue kasutaja loomisel tekkis viga: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function sign_in($email, $password) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT id, firstname, lastname, password FROM vpr_users WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->bind_result(
        $id_from_db,
        $firstname_from_db,
        $lastname_from_db,
        $password_from_db
    );
    echo $conn->error;
    $stmt->execute();
    if ($stmt->fetch()) {
        // kasutaja on olemas, kontrollime parooli
        if (password_verify($password, $password_from_db)) {
            // ongi õige
            $_SESSION["user_id"] = $id_from_db;
            $_SESSION["user_firstname"] = $firstname_from_db;
            $_SESSION["user_lastname"] = $lastname_from_db;
            $stmt->close();
            /**
             * siin edaspidi sisselogimisel pärime SQL-iga kasutaja profiili,
             * kui see on olemas siis loeme sealt tausta- ja tekstivärvid,
             * muidu kasutame mingeid vaikevärve
             */
            $stmt = $conn->prepare("SELECT bgcolor, txtcolor FROM vpr_userprofiles WHERE userid = ?");
            $stmt->bind_param("i", $_SESSION["user_id"]);
            $stmt->bind_result($bgcolor_from_db, $txtcolor_from_db);
            $stmt->execute();
            $_SESSION["bg_color"] = "#FFFFFF";
            $_SESSION["text_color"] = "#000000";
            if ($stmt->fetch()) {
                if (!empty($bgcolor_from_db)) {
                    $_SESSION["bg_color"] = $bgcolor_from_db;
                }
                if (!empty($txtcolor_from_db)) {
                    $_SESSION["text_color"] = $txtcolor_from_db;
                }
            }
            $stmt->close();
            $conn->close();
            header("Location: home.php");
            exit();
        } else {
            $notice = "Kasutajanimi või salasõna oli vale!";
        }
    } else {
        $notice = "Kasutajanimi või salasõna oli vale!";
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function save_user_profile($description, $bg_color, $text_color) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare("SELECT id FROM vpr_userprofiles WHERE userid = ?");
    echo $conn->error;
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        $stmt->close();
        $stmt = $conn->prepare("UPDATE vpr_userprofiles SET description = ?, bgcolor = ?, txtcolor = ? WHERE userid = ?");
        echo $conn->error;
        $stmt->bind_param("sssi", $description, $bg_color, $text_color, $_SESSION["user_id"]);
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO vpr_userprofiles (userid, description, bgcolor, txtcolor) VALUES(?,?,?,?)");
        echo $conn->error;
        $stmt->bind_param("isss", $_SESSION["user_id"], $description, $bg_color, $text_color);
    }
    if ($stmt->execute()) {
        $notice = "Profiil salvestatud!";
    } else {
        $notice = "Profiili salvestamisel tekkis viga: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function get_user_description() {
    $notice = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare("SELECT description FROM vpr_userprofiles WHERE userid = ?");
    echo $conn->error;
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->bind_result($description_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        $notice = $description_from_db;
    }
    $stmt->close();
    $conn->close();
    return $notice;
}
