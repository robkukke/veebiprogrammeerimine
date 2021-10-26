<?php
$database = "if21_robin_ku";
require_once "fnc_general.php";

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
        $options_html .= ">" . $first_name_from_db . " " . $last_name_from_db . " (" . date_to_est_format($birth_date_from_db) . ")</option> \n";
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
    // <option value="x" selected>Film (aasta)</option>
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
    // <option value="x" selected>Amet</option>
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

function read_all_genre_for_option($selected) {
    $options_html = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    // <option value="x" selected>Žanr</option>
    $stmt = $conn->prepare("SELECT id, genre_name FROM genre");
    $stmt->bind_result($id_from_db, $genre_name_from_db);
    $stmt->execute();
    while ($stmt->fetch()) {
        $options_html .= '<option value="' . $id_from_db . '"';
        if ($selected == $id_from_db) {
            $options_html .= " selected";
        }
        $options_html .= ">" . $genre_name_from_db . "</option> \n";
    }
    $stmt->close();
    $conn->close();
    return $options_html;
}

function read_all_person_in_movie_for_option($selected) {
    $options_html = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT id, role FROM person_in_movie WHERE role IS NOT NULL"
    );
    echo $conn->error;
    $stmt->bind_result($id_from_db, $role_from_db);
    $stmt->execute();
    // <option value="x" selected>Roll</option>
    while ($stmt->fetch()) {
        $options_html .= '<option value="' . $id_from_db . '"';
        if ($id_from_db == $selected) {
            $options_html .= " selected";
        }
        $options_html .= ">" . $role_from_db . "</option> \n";
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

function store_movie_genre($selected_movie, $selected_genre) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    // <option value="x" selected>Žanr</option>
    $stmt = $conn->prepare(
        "SELECT id FROM movie_genre WHERE movie_id = ? AND genre_id = ?"
    );
    $stmt->bind_param("ii", $selected_movie, $selected_genre);
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        // selline on olemas
        $notice = "Selline seos on juba olemas!";
    } else {
        $stmt->close();
        $stmt = $conn->prepare(
            "INSERT INTO movie_genre (movie_id, genre_id) VALUES (?, ?)"
        );
        $stmt->bind_param("ii", $selected_movie, $selected_genre);
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

function store_movie($title, $production_year, $duration, $description) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT id FROM movie WHERE title = ? AND production_year = ? AND duration = ? AND description = ?"
    );
    $stmt->bind_param(
        "siis",
        $title,
        $production_year,
        $duration,
        $description
    );
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        $notice = "Selline film on juba olemas!";
    } else {
        $stmt->close();
        $stmt = $conn->prepare(
            "INSERT INTO movie (title, production_year, duration, description) VALUES(?,?,?,?)"
        );
        echo $conn->error;
        $stmt->bind_param(
            "siis",
            $title,
            $production_year,
            $duration,
            $description
        );
        if ($stmt->execute()) {
            $notice = "Lisamine õnnestus!";
        } else {
            $notice = "Lisamisel tekkis viga: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function store_person($first_name, $last_name, $birth_date) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT id FROM person WHERE first_name = ? AND last_name = ? AND birth_date = ?"
    );
    $stmt->bind_param("sss", $first_name, $last_name, $birth_date);
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        $notice = "Selline näitleja on juba olemas!";
    } else {
        $stmt->close();
        $stmt = $conn->prepare(
            "INSERT INTO person (first_name, last_name, birth_date) VALUES(?,?,?)"
        );
        echo $conn->error;
        $stmt->bind_param("sss", $first_name, $last_name, $birth_date);
        if ($stmt->execute()) {
            $notice = "Lisamine õnnestus!";
        } else {
            $notice = "Lisamisel tekkis viga: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function store_genre($genre_name, $description) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT id FROM genre WHERE genre_name = ? AND description = ?"
    );
    $stmt->bind_param("ss", $genre_name, $description);
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        $notice = "Selline žanr on juba olemas!";
    } else {
        $stmt->close();
        $stmt = $conn->prepare(
            "INSERT INTO genre (genre_name, description) VALUES(?,?)"
        );
        echo $conn->error;
        $stmt->bind_param("ss", $genre_name, $description);
        if ($stmt->execute()) {
            $notice = "Lisamine õnnestus!";
        } else {
            $notice = "Lisamisel tekkis viga: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function store_quote($quote_text, $person_in_movie_id) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT id FROM quote WHERE quote_text = ? AND person_in_movie_id = ?"
    );
    $stmt->bind_param("si", $quote_text, $person_in_movie_id);
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        $notice = "Selline tsitaat on juba olemas!";
    } else {
        $stmt->close();
        $stmt = $conn->prepare(
            "INSERT INTO quote (quote_text, person_in_movie_id) VALUES(?,?)"
        );
        echo $conn->error;
        $stmt->bind_param("si", $quote_text, $person_in_movie_id);
        if ($stmt->execute()) {
            $notice = "Lisamine õnnestus!";
        } else {
            $notice = "Lisamisel tekkis viga: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function store_position($position_name, $description) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT id FROM position WHERE position_name = ? AND description = ?"
    );
    $stmt->bind_param("ss", $position_name, $description);
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        $notice = "Selline amet on juba olemas!";
    } else {
        $stmt->close();
        $stmt = $conn->prepare(
            "INSERT INTO position (position_name, description) VALUES(?,?)"
        );
        echo $conn->error;
        $stmt->bind_param("ss", $position_name, $description);
        if ($stmt->execute()) {
            $notice = "Lisamine õnnestus!";
        } else {
            $notice = "Lisamisel tekkis viga: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function store_production_company($company_name, $company_address) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT id FROM production_company WHERE company_name = ? AND company_address = ?"
    );
    $stmt->bind_param("ss", $company_name, $company_address);
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        $notice = "Selline tootja on juba olemas!";
    } else {
        $stmt->close();
        $stmt = $conn->prepare(
            "INSERT INTO production_company (company_name, company_address) VALUES(?,?)"
        );
        echo $conn->error;
        $stmt->bind_param("ss", $company_name, $company_address);
        if ($stmt->execute()) {
            $notice = "Lisamine õnnestus!";
        } else {
            $notice = "Lisamisel tekkis viga: " . $stmt->error;
        }
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function list_person_movie_info() {
    $html = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT person.first_name, person.last_name, person.birth_date, person_in_movie.role, movie.title, movie.production_year, movie.duration FROM person JOIN person_in_movie ON person.id = person_in_movie.person_id JOIN movie ON movie.id = person_in_movie.movie_id"
    );
    echo $conn->error;
    $stmt->bind_result(
        $first_name_from_db,
        $last_name_from_db,
        $birth_date_from_db,
        $role_from_db,
        $title_from_db,
        $production_year_from_db,
        $duration_from_db
    );
    $stmt->execute();
    while ($stmt->fetch()) {
        $html .= "<li>" . $first_name_from_db . " " . $last_name_from_db . " (sündinud: " . date_to_est_format($birth_date_from_db) . "), ";
        if (!empty($role_from_db)) {
            $html .= "tegelane " . $role_from_db . ' filmis "' . $title_from_db . '" (toodetud: ' . $production_year_from_db . ", kestus: " . duration_min_to_hour_and_min($duration_from_db) . ")";
        } else {
            $html .= '"' . $title_from_db . '" (toodetud: ' . $production_year_from_db . ", kestus: " . duration_min_to_hour_and_min($duration_from_db) . ")";
        }
        $html .= "</li> \n";
    }
    if (empty($html)) {
        $html = "<p>Info puudub.</p> \n";
    } else {
        $html = "<ul> \n" . $html . "</ul> \n";
    }
    $stmt->close();
    $conn->close();
    return $html;
}
