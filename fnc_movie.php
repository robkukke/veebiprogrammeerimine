<?php
$database = "if21_robin_ku";

function read_all_person_for_option($selected) {
    $options_html = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare("SELECT * FROM person");
    echo $conn->error;
    $stmt->bind_result(
        $id_from_db,
        $first_name_from_db,
        $last_name_from_db,
        $birth_date_from_db
    );
    $stmt->execute();
    // <option value="x" selected>Eesnimi Perekonnanimi (sünniaeg)</option>
    while ($stmt->fetch()) {
        $options_html .= '<option value="' . $id_from_db . '"';
        if ($id_from_db == $selected) {
            $options_html .= " selected";
        }
        $options_html .= ">" . $first_name_from_db . " " . $last_name_from_db . " (" . $birth_date_from_db . ")</option> \n";
    }
    $stmt->close();
    $conn->close();
    return $options_html;
}

function read_all_movie_for_option($selected) {
    $options_html = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    // <option value="x" selected>Film</option>
    $stmt = $conn->prepare("SELECT id, title, production_year FROM movie");
    $stmt->bind_result($id_from_db, $title_from_db, $production_year_from_db);
    $stmt->execute();
    while ($stmt->fetch()) {
        $options_html .= '<option value="' . $id_from_db . '"';
        if ($selected == $id_from_db) {
            $options_html .= " selected";
        }
        $options_html .= ">" . $title_from_db . " (" . $production_year_from_db . ")</option> \n";
    }
    $stmt->close();
    $conn->close();
    return $options_html;
}

function read_all_position_for_option($selected) {
    $options_html = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    // <option value="x" selected>Film</option>
    $stmt = $conn->prepare("SELECT id, position_name FROM position");
    $stmt->bind_result($id_from_db, $position_name_from_db);
    $stmt->execute();
    while ($stmt->fetch()) {
        $options_html .= '<option value="' . $id_from_db . '"';
        if ($selected == $id_from_db) {
            $options_html .= " selected";
        }
        $options_html .= ">" . $position_name_from_db . "</option> \n";
    }
    $stmt->close();
    $conn->close();
    return $options_html;
}

function store_person_in_movie(
    $selected_person,
    $selected_movie,
    $selected_position,
    $role
) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    // <option value="x" selected>Film</option>
    $stmt = $conn->prepare(
        "SELECT id FROM person_in_movie WHERE person_id = ? AND movie_id = ? AND position_id = ? AND role = ?"
    );
    $stmt->bind_param(
        "iiis",
        $selected_person,
        $selected_movie,
        $selected_position,
        $role
    );
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        // selline on olemas
        $notice = "Selline seos on juba olemas!";
    } else {
        $stmt->close();
        $stmt = $conn->prepare(
            "INSERT INTO person_in_movie (person_id, movie_id, position_id, role) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iiis",
            $selected_person,
            $selected_movie,
            $selected_position,
            $role
        );
        if ($stmt->execute()) {
            $notice = "Uus seos edukalt salvestatud!";
        } else {
            $notice = "Uue seose salvestamisle tekkis viga: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function store_person_photo($file_name, $person_id) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "INSERT INTO picture (picture_file_name, person_id) VALUES (?, ?)"
    );
    $stmt->bind_param("si", $file_name, $person_id);
    if ($stmt->execute()) {
        $notice = "Uus foto edukalt salvestatud!";
    } else {
        $notice = "Uue foto andmebaasi salvestamisel tekkis viga: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    return $notice;
}
