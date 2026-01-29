<?php
$servername = "localhost";
$username = "dem_user";
$password = "secret";
$dbname = "dem_ex";
// '127.0.0.1', 'root', '', 'cleaning_service'

$db = new mysqli($servername, $username, $password, $dbname);

if ($db->connect_error) {
  die('Connection failed: ' . $db->connect_error);
}

$db->set_charset('utf8mb4');
?>