<?php
$host = "localhost";
$user = "root";
$pass = "";
$database = "greensaver_db";

$con = mysqli_connect($host, $user, $pass, $database);

if (!$con) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>