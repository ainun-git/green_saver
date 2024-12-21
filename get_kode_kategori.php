<?php
include 'koneksi.php';

$kategori = $_GET['kategori'] ?? '';

$query = "SELECT kodeKategori, namaKategori FROM KategoriSampah WHERE LOWER(kategori) = LOWER('$kategori')";
$result = mysqli_query($con, $query);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
