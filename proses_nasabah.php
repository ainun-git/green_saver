<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $idNasabah = isset($_POST['IdNasabah']) ? $_POST['IdNasabah'] : '';
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $nomorInduk = mysqli_real_escape_string($con, $_POST['nomorInduk']);
    $alamat = mysqli_real_escape_string($con, $_POST['alamat']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Check if this is an edit (update) operation
    if (!empty($idNasabah)) {
        // Update operation
        if (!empty($password)) {
            // If password is provided, update it along with other fields
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE Nasabah SET 
                     nama = ?, 
                     nomorInduk = ?,
                     alamat = ?,
                     username = ?,
                     password = ?
                     WHERE IdNasabah = ?";
            
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "ssssss", $nama, $nomorInduk, $alamat, $username, $hashedPassword, $idNasabah);
        } else {
            // If no password provided, update other fields only
            $query = "UPDATE Nasabah SET 
                     nama = ?, 
                     nomorInduk = ?,
                     alamat = ?,
                     username = ?
                     WHERE IdNasabah = ?";
            
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "sssss", $nama, $nomorInduk, $alamat, $username, $idNasabah);
        }

        if (mysqli_stmt_execute($stmt)) {
            header("Location: Daftar_Nasabah.php?status=success&message=Data nasabah berhasil diperbarui!");
            exit();
        } else {
            header("Location: Daftar_Nasabah.php?status=error&message=Gagal mengupdate data");
            exit();
        }
    } else {
        // Insert new record
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Generate new IdNasabah
        $query = "SELECT MAX(IdNasabah) as max_id FROM Nasabah";
        $result = mysqli_query($con, $query);
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['max_id'];
        $nextId = 'N' . str_pad((intval(substr($lastId, 1)) + 1), 5, '0', STR_PAD_LEFT);

        $query = "INSERT INTO Nasabah (IdNasabah, nama, nomorInduk, alamat, username, password) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $nextId, $nama, $nomorInduk, $alamat, $username, $hashedPassword);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: Daftar_Nasabah.php?status=success&message=Data nasabah baru berhasil ditambahkan!");
            exit();
        } else {
            header("Location: Daftar_Nasabah.php?status=error&message=Gagal menambahkan data");
            exit();
        }
    }
} else {
    // If accessed directly without POST data
    header("Location: Daftar_Nasabah.php");
    exit();
}
?>