<?php
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = mysqli_real_escape_string($con, trim($_POST['nama']));
  $nomorInduk = mysqli_real_escape_string($con, trim($_POST['nomorInduk']));
  $alamat = mysqli_real_escape_string($con, trim($_POST['alamat']));
  $username = mysqli_real_escape_string($con, trim($_POST['username']));
  $password = $_POST['password'];
  if (empty($nama) || empty($nomorInduk) || empty($alamat) || empty($username) || empty($password)) {
    echo "<script>alert('Semua field harus diisi!'); window.history.back();</script>";
    exit();
  }

  $checkUsername = "SELECT * FROM Nasabah WHERE username = '$username'";
  $result = mysqli_query($con, $checkUsername);
  if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('Username sudah digunakan!'); window.history.back();</script>";
    exit();
  }

  $query = "SELECT IdNasabah FROM Nasabah ORDER BY IdNasabah DESC LIMIT 1";
  $result = mysqli_query($con, $query);
  $lastId = mysqli_fetch_assoc($result)['IdNasabah'];

  if ($lastId) {
    $idNumber = (int) substr($lastId, 2) + 1;
    $newId = "NS" . str_pad($idNumber, 3, '0', STR_PAD_LEFT);
  } else {
    $newId = "NS001";
  }

  $saldoAwal = 0.0;
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Enkripsi password

  $insertQuery = "INSERT INTO Nasabah (IdNasabah, nama, nomorInduk, alamat, saldo, username, password) 
                    VALUES ('$newId', '$nama', '$nomorInduk', '$alamat', $saldoAwal, '$username', '$hashedPassword')";

  if (mysqli_query($con, $insertQuery)) {
    echo "<script>alert('Pendaftaran berhasil!'); window.location.href = 'login.php';</script>";
  } else {
    echo "<script>alert('Pendaftaran gagal: " . mysqli_error($con) . "'); window.history.back();</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Green Saver</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #FBFDF6;
    margin: 0;
    padding: 0;
  }

  .container-fluid {
    background-color: #F7FFE5;
  }

  .bg-light-green {
    background-color: #FBFDF6;
  }

  .welcome-text {
    font-size: 2.5rem;
    font-weight: bold;
    color: #51803A;
  }

  .image-placeholder {
    width: 250px;
    height: 250px;
    border-radius: 50%;
    margin-top: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-image: url('logo.png');
    background-size: 130%;
    background-position: center;
    background-repeat: no-repeat;
  }

  .sign-form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  .sign-form h2 {
    font-weight: bold;
    color: #333;
  }

  .sign-form p {
    font-size: 14px;
    color: #666;
  }

  .input-group-text {
    border: none;
    background-color: #cde4d4;
    color: #4b8c5a;
  }

  .forgot-password {
    font-size: 14px;
    color: #4b8c5a;
    text-decoration: none;
  }

  .forgot-password:hover {
    text-decoration: underline;
  }

  .signup-link {
    color: #4b8c5a;
    font-weight: bold;
    text-decoration: none;
  }

  .signup-link:hover {
    text-decoration: underline;
  }

  .btn.w-100 {
    background-color: #51803A;
    color: white;
  }
</style>

<body>
  <div class="container-fluid vh-100">
    <div class="row h-100">
      <div class="col-md-6 d-flex flex-column justify-content-center align-items-center bg-light-green">
        <h1 class="welcome-text text-uppercase">Welcome</h1>
        <div class="image-placeholder"></div>
      </div>

      <div class="col-md-6 d-flex flex-column justify-content-center px-5">
        <div class="sign-form">
          <h2 class="text-center">Create Account</h2>

          <form method="POST" action="">
            <div class="form-group mb-3">
              <div class="input-group">
                <span class="input-group-text bg-light-green">
                  <i class="bx bx-user"></i>
                </span>
                <input type="text" class="form-control" name="nama" placeholder="Nama Lengkap" required>
              </div>
            </div>

            <div class="form-group mb-3">
              <div class="input-group">
                <span class="input-group-text bg-light-green">
                  <i class=" bx bx-shield-quarter"></i>
                </span>
                <input type="text" class="form-control" name="nomorInduk" placeholder="Nomor Induk" required>
              </div>
            </div>

            <div class="form-group mb-3">
              <div class="input-group">
                <span class="input-group-text bg-light-green">
                  <i class='bx bx-home-alt'></i>
                </span>
                <input type="text" class="form-control" name="alamat" placeholder="Alamat" required>
              </div>
            </div>

            <div class="form-group mb-3">
              <div class="input-group">
                <span class="input-group-text bg-light-green">
                  <i class="bx bx-user"></i>
                </span>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
              </div>
            </div>

            <div class="form-group mb-3">
              <div class="input-group">
                <span class="input-group-text bg-light-green">
                  <i class="bx bx-lock"></i>
                </span>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
            </div>
            <button type="submit" class="btn w-100">Sign Up</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>