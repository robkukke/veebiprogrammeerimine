<?php
function test_input($data) {
    $data = htmlspecialchars($data);
    $data = stripslashes($data);
    return trim($data);
}
