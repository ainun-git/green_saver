<?php
include 'koneksi.php';

session_start();
if (!isset($_SESSION['IdNasabah'])) {
  header('Location: editNasabah.php');
  exit();
}
$user_id = $_SESSION['IdNasabah'];
$query = "SELECT * FROM nasabah WHERE IdNasabah = '$user_id'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $nama_lengkap = isset($_POST['nama']) ? $_POST['nama'] : '';
  $no_induk = isset($_POST['nomorInduk']) ? $_POST['nomorInduk'] : '';
  $alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';
  $username = isset($_POST['username']) ? $_POST['username'] : '';

  $update_query = "UPDATE nasabah SET nama='$nama_lengkap', nomorInduk='$no_induk', alamat='$alamat', username='$username' WHERE IdNasabah='$user_id'";

  if (mysqli_query($con, $update_query)) {
    $_SESSION['username'] = $username;
    $success_message = "Data berhasil diperbarui!";
  } else {
    $error_message = "Terjadi kesalahan saat memperbarui data!";
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Data</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styleEdit.css">
</head>
<style>
  body {
    font-family: Arial, sans-serif;
    background-color: #FBFDF6;
    margin: 0;
  }

  .bg-light-green {
    background-color: #f6fcef;
  }

  .input-box {
    background-color: #d8e9d3;
    border: none;
    border-radius: 10px;
    padding: 10px;
  }

  .input-box:focus {
    border: 2px solid #6db076;
    outline: none;
  }

  .form-floating label {
    color: #6c757d;
  }

  .back-icon i {
    font-size: 24px;
    color: #6db076;
    text-decoration: none;
    margin-top: 20px;
  }

  .back-icon:hover i {
    color: #4b8c5a;
  }

  .bi-person-circle {
    font-size: 24px;
    color: #6db076;
  }

  h2 {
    color: #4b8c5a;
    font-weight: bold;
  }

  button {
    background-color: #4b8c5a;
    border: none;
    font-size: 16px;
    font-weight: bold;
  }

  button:hover {
    background-color: #3a7046;
  }

  .row {
    margin: 0;
  }

  .vh-100 {
    height: 100vh;
  }

</style>

<body>
  <div class="container-fluid vh-100 bg-light-green">
    <div class="row align-items-center px-4 py-2">
      <div class="col-auto">
        <a href="dashboardNasabah.php" class="back-icon">
          <i class="bx bx-arrow-back"></i>
        </a>
      </div>
    </div>

    <div class="row justify-content-center align-items-center vh-75">
      <div class="col-md-6">
        <h2 class="text-center mb-4">Edit Data</h2>

        <?php if (isset($success_message)): ?>
          <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif (isset($error_message)): ?>
          <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
          <div class="form-floating mb-3">
            <input type="text" class="form-control input-box" id="namaLengkap" name="nama" value="<?php echo $user['nama']; ?>" placeholder="Nama Lengkap">
            <label for="namaLengkap">Nama Lengkap</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control input-box" id="noInduk" name="nomorInduk" value="<?php echo $user['nomorInduk']; ?>" placeholder="No. Induk">
            <label for="noInduk">No. Induk</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control input-box" id="alamat" name="alamat" value="<?php echo $user['alamat']; ?>" placeholder="Alamat">
            <label for="alamat">Alamat</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control input-box" id="username" name="username" value="<?php echo $user['username']; ?>" placeholder="Username">
            <label for="username">Username</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control input-box" id="passwordBaru" name="passwordBaru" placeholder="Password Baru">
            <label for="passwordBaru">Password Baru</label>
          </div>
          <div class="form-floating mb-4">
            <input type="password" class="form-control input-box" id="konfirmasiPassword" name="konfirmasiPassword" placeholder="Konfirmasi Password Baru">
            <label for="konfirmasiPassword">Konfirmasi Password Baru</label>
          </div>
          <button type="submit" class="btn btn-success w-100 py-2">SIMPAN</button>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($con);
?>