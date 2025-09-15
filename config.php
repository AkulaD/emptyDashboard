<?php 
$db = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "paketempty";

$conn = new mysqli($db, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
