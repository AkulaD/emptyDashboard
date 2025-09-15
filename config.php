<?php 
$db = "localhost";
$dbuser = "paketuser";
$dbpass = "Paket@1234";
$dbname = "paketempty";

$conn = new mysqli($db, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>