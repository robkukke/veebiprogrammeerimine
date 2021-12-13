<?php
$database = "if21_robin_ku";

function register_to_party($name, $surname, $code) {
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "INSERT INTO vpr_party (firstname, lastname, studentcode) VALUES(?,?,?)"
    );
    echo $conn->error;
    $stmt->bind_param("ssi", $name, $surname, $code);
    if ($stmt->execute()) {
        $notice = "Oled registreerunud! Maksa peatselt ka osavõtutasu!";
    } else {
        $notice = "Registreerumisel tekkis viga: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function registered_data() {
    $notice = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT COUNT(id) from vpr_party WHERE cancelled IS NULL"
    );
    $stmt->bind_result($registered);
    $stmt->execute();
    if ($stmt->fetch()) {
        $notice = $registered;
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function paid_data() {
    $notice = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT COUNT(payment) from vpr_party WHERE cancelled IS NULL"
    );
    $stmt->bind_result($paid);
    $stmt->execute();
    if ($stmt->fetch()) {
        $notice = $paid;
    }
    $stmt->close();
    $conn->close();
    return $notice;
}

function set_payment($id) {
    $paid = 1;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare("UPDATE vpr_party SET payment = ? WHERE id = ?");
    echo $conn->error;
    $stmt->bind_param("ii", $paid, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function list_registered() {
    $list_html = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT firstname, lastname, payment from vpr_party WHERE cancelled IS NULL"
    );
    $stmt->bind_result($firstname_from_db, $lastname_from_db, $paid);
    $stmt->execute();
    while ($stmt->fetch()) {
        $list_html .= "<li>" . $firstname_from_db . " " . $lastname_from_db;
        if (!empty($paid)) {
            $list_html .= ", MAKSTUD";
        }
        $list_html .= "</li> \n";
    }
    if (!empty($list_html)) {
        $list_html = "<ol> \n" . $list_html . "</ol> \n";
    }
    $stmt->close();
    $conn->close();
    return $list_html;
}

function list_for_payment() {
    $list_html = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT id, firstname, lastname from vpr_party WHERE payment IS NULL AND cancelled IS NULL"
    );
    $stmt->bind_result($id_from_db, $firstname_from_db, $lastname_from_db);
    $stmt->execute();
    while ($stmt->fetch()) {
        $list_html .= '<option value="' . $id_from_db . '">' . $firstname_from_db . " " . $lastname_from_db . "</option> \n";
    }
    $stmt->close();
    $conn->close();
    return $list_html;
}

function forms_for_payment() {
    $list_html = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare(
        "SELECT id, firstname, lastname, payment from vpr_party WHERE cancelled IS NULL"
    );
    $stmt->bind_result(
        $id_from_db,
        $firstname_from_db,
        $lastname_from_db,
        $payment_from_db
    );
    $stmt->execute();
    while ($stmt->fetch()) {
        if (empty($payment_from_db)) {
            $list_html .= '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">' . "\n";
            $list_html .= '<input name="id_input" type="hidden" value="' . $id_from_db . '">' . "\n";
            $list_html .= "<span>" . $firstname_from_db . " " . $lastname_from_db . "</span> \n";
            $list_html .= '<input name="payment_submit" type="submit" value="Märgi maksnuks">' . "\n";
            $list_html .= "</form> \n";
        } else {
            $list_html .= "<p>" . $firstname_from_db . " " . $lastname_from_db . "</p> \n";
        }
    }
    if (empty($list_html)) {
        $list_html = "<p>Kahjuks pole peole registreerunuid!</p> \n";
    }
    $stmt->close();
    $conn->close();
    return $list_html;
}

function cancel_registration($code) {
    $notice = null;
    $conn = new mysqli(
        $GLOBALS["server_host"],
        $GLOBALS["server_user_name"],
        $GLOBALS["server_password"],
        $GLOBALS["database"]
    );
    $conn->set_charset("utf8");
    $stmt = $conn->prepare("SELECT id from vpr_party WHERE studentcode = ?");
    echo $conn->error;
    $stmt->bind_param("i", $code);
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if ($stmt->fetch()) {
        $stmt->close();
        $stmt = $conn->prepare(
            "UPDATE vpr_party SET cancelled = NOW() WHERE studentcode = ?"
        );
        echo $conn->error;
        $stmt->bind_param("i", $code);
        if ($stmt->execute()) {
            $notice = "Tühistatud!";
        }
    } else {
        $notice = "Sellise koodiga üliõpilast pole peole registreerunud!";
    }
    $stmt->close();
    $conn->close();
    return $notice;
}
