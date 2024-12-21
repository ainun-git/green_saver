<?php
session_start();
include "koneksi.php";

if (isset($_POST['action']) && isset($_POST['id'])) {
  $idPenarikan = $_POST['id'];
  $status = ($_POST['action'] === 'terima') ? 'Diterima' : 'Ditolak';

  mysqli_begin_transaction($con);
  try {
    $query = "UPDATE Penarikan SET status = '$status' WHERE IdPenarikan = '$idPenarikan'";

    if (!mysqli_query($con, $query)) {
      throw new Exception("Gagal mengupdate status");
    }
    if ($status === 'Diterima') {
      $query_penarikan = "SELECT * FROM Penarikan WHERE IdPenarikan = '$idPenarikan'";
      $result_penarikan = mysqli_query($con, $query_penarikan);
      $data_penarikan = mysqli_fetch_assoc($result_penarikan);

      $idTransaksi = generateIdTransaksi($con);

      $query_transaksi = "INSERT INTO RiwayatTransaksi (IdTransaksi, IdNasabah, jenisTransaksi, jumlah, tanggalTransaksi) 
                             VALUES ('$idTransaksi', '{$data_penarikan['IdNasabah']}', 'Penarikan', {$data_penarikan['jumlah']}, CURDATE())";

      if (!mysqli_query($con, $query_transaksi)) {
        throw new Exception("Gagal menambahkan riwayat transaksi: " . mysqli_error($con));
      }
    }

    mysqli_commit($con);
    $_SESSION['pesan'] = "Status penarikan berhasil diupdate";
  } catch (Exception $e) {
    mysqli_rollback($con);
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
  }

  header("Location: accPenarikan.php");
  exit();
}
$query = "SELECT p.*, n.nama as nama_nasabah 
          FROM Penarikan p
          JOIN Nasabah n ON p.IdNasabah = n.IdNasabah
          ORDER BY p.tanggalPenarikan DESC";
$result = mysqli_query($con, $query);

function generateIdTransaksi($con)
{
  $query = "SELECT MAX(IdTransaksi) AS max_id FROM RiwayatTransaksi";
  $result = mysqli_query($con, $query);
  $data = mysqli_fetch_assoc($result);
  $max_id = $data['max_id'];

  if (empty($max_id)) {
    return 'T001';
  }
  if (strlen($max_id) < 2) {
    return 'T001';
  }

  try {
    $nomor = (int)substr($max_id, 1);
    $nomor++;
    return 'T' . sprintf('%03d', $nomor);
  } catch (Exception $e) {
    return 'T001';
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Penarikan Poin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
      background-color: #FFFFFF;
    }

    .hamburger-menu {
      font-size: 25px;
      color: #567B65;
      background-color: transparent;
      border: none;
      position: fixed;
      top: 20px;
      left: 40px;
      z-index: 1000;
      cursor: pointer;
    }

    .sidebar {
      position: fixed;
      width: 250px;
      left: -259px;
      top: 0;
      background-color: #F7FFE5;
      color: #567B65;
      height: 100%;
      padding: 0;
      box-sizing: border-box;
      border-radius: none;
      transition: left 0.3s ease;
      z-index: 999;

    }

    .sidebar.active {
      left: 0;
    }

    .main-content {
      margin-left: 0;
      transition: margin-left 0.3s ease;
    }

    .main-content.sidebar-active {
      margin-left: 250px;
    }

    .sidebar-menu {
      padding-top: 2px;
    }

    .sidebar .logo h2 {
      margin: 30px;
      font-size: 20px;
      color: #4CAF50;
    }

    .menu-container,
    .submenu-container {
      padding: 0;
      font-weight: bold;
      color: #567B65;

    }

    .main-menu a {
      color: #567B65;
      text-decoration: none;
      width: 95%;
      display: block;
      padding: 10px 15px;
    }

    .menu-container li i {
      margin-right: 10px;
      font-size: 20px;
    }

    .menu-container .submenu-container li i {
      margin-right: 10px;
      font-size: 20px;
    }

    .main-menu:hover {
      border-left: 4px solid #567B65;

    }

    .parent-menu a {
      color: #567B65;
      text-decoration: none;
      width: 50%;
      display: block;
      padding: 10px 15px;
    }

    .parent-menu div {
      display: flex;
      align-items: center;

    }

    .parent-menu bi-people-fill {
      padding-left: 60px;
    }

    .parent-menu:hover {
      border-left: 4px solid #567B65;

    }

    .parent-menu.open .custom-caret {
      transform: rotate(180deg);
    }

    .custom-caret {
      font-size: 20px;
      margin-left: 90px;
      color: #567B65;
      transition: transform 0.3s ease;
      padding-left: 0;

    }

    .submenu-container:hover {
      border-left: none;
    }

    .footer-menu div {
      padding-bottom: 100%;
    }

    .footer-menu a {
      color: #567B65;
      text-decoration: none;
      width: 100%;
      display: block;
      padding: 10px 15px;

    }

    .footer-menu:hover {
      border-left: 4px solid #567B65;

    }

    .submenu-container .submenu {
      font-size: small;
      white-space: nowrap;
      padding-left: 20px;
      border-left: none;
    }

    .hidden {
      display: none;
    }

    .show {
      display: block;
    }

    .main-content {
      flex: 1;
      margin-top: 1cm;
      padding-bottom: 20px;
      box-sizing: border-box;
      color: #567B65;
    }

    header {
      display: flex;
      justify-content: right;
      align-items: center;
      margin-bottom: 20px;
    }

    header p {
      margin: 0;
      font-size: 16px;
      font-weight: bold;
      color: #567B65;
      text-align: right;

    }

    header i {
      font-size: 30px;
    }

    .nasabah-section {
      background-color: #dcdcdc;



    }

    .nasabah-section h2 {
      color: #567B65;
      text-align: center;
      padding-top: 8px;
    }

    .nasabah-section .transaksi {
      color: #567B65;
      display: flex;
      justify-content: center;
      height: 50px;
      width: 100%;

    }

    .nasabah-section .poin a {
      color: #567B65;
      text-decoration: none;
      border-bottom: 3px solid#567B65;

    }

    .nasabah-section .poin a:hover {
      border-bottom: 3px solid #567B65;
      content: '';
    }

    .nasabah-section .saldo a {
      color: #567B65;
      text-decoration: none;

    }

    .nasabah-section .saldo a:hover {
      border-bottom: 3px solid #567B65;
    }

    .penarikan h2 {
      color: #567B65;
      margin-left: 40px;

    }

    .nasabah-table {
      width: 95%;
      border-collapse: collapse;
      margin-left: 2%;
    }

    .nasabah-table th,
    .nasabah-table td {
      padding: 10px;
      margin-left: 40px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    .nasabah-table th {
      text-align: center;
      background-color: #f2f2f2;
      color: #567B65;
    }

    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 10px;
    }

    .btn-action {
      padding: 5px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .btn-terima {
      background-color: #28a745;
      color: white;
    }

    .btn-tolak {
      background-color: #dc3545;
      color: white;
    }
  </style>
</head>

<body>
  <button class="hamburger-menu" onclick="toggleSidebar()">
    &#9776;
  </button>

  <div class="sidebar">
    <div class="logo">
      <h2>GREEN SAVER</h2>
    </div>

    <div class="sidebar-menu">
      <ul class="menu-container">
        <li class="main-menu">
          <a href="dashboardPengurus.php"><i class="bi bi-house-door"></i>Dashboard</a>
        </li>

        <li class="parent-menu" onclick="toggleSubMenu(event)">
          <div>
            <a href="#"><i class="bi bi-people-fill"></i>Nasabah</a>
            <i class="bi bi-chevron-down custom-caret"></i></i>
          </div>

          <div>
            <ul class="submenu-container" hidden>
              <li class="submenu"><a href="Daftar_Nasabah.php"><i class="bi bi-person-square"></i>Daftar Nasabah</a></li>
              <li class="submenu"><a href="accPenarikan.php"><i class="bi bi-file-earmark-diff"></i>Permintaan</a></li>
            </ul>
          </div>
        </li>

        <li class="parent-menu" onclick="toggleSubMenu(event)">
          <div>
            <a href="#"><i class="bi bi-recycle"></i>Sampah</a>
            <i class="bi bi-chevron-down custom-caret"></i>
          </div>

          <div>
            <ul class="submenu-container" hidden id="submenu-sampah">
              <li class="submenu"><a href="SetoranSampah.php"><i class="bi bi-file-earmark-plus"></i>Setoran Sampah</a></li>
              <li class="submenu"><a href="kategori.php"><i class="bi bi-collection"></i>Kategori</a></li>
            </ul>
          </div>
        </li>

        <li class="main-menu">
          <a href="Penjualan.php"><i class="bi bi-currency-dollar"></i>Penjualan</a>
        </li>

        <li class="main-menu">
          <a href="RiwayatTransaksi.php"><i class="bi bi-clock-history"></i>Riwayat</a>
        </li>

        <li class="footer-menu">
          <a href="login.php"> <i class="bi bi-box-arrow-left"></i>Log Out</a>
        </li>
      </ul>
    </div>
  </div>
  <div class="main-content">
    <header>
      <div class="profile">
        <p>Bank Sampah Cahaya Mandiri <i class="bi bi-person-fill"></i></p>
      </div>
    </header>

    <div class="nasabah-section">
      <h2>Transaksi Penarikan</h2>
    </div>

    <div class="penarikan">
      <table class="nasabah-table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Nama</th>
            <th>Jenis Penarikan</th>
            <th>Jumlah</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr>";
              echo "<td>" . $row['tanggalPenarikan'] . "</td>";
              echo "<td>" . $row['nama_nasabah'] . "</td>";
              echo "<td>" . ($row['jenisPenarikan'] === 'PenarikanSaldo' ? 'Penarikan Saldo' : 'Penukaran Poin') . "</td>";
              echo "<td>Rp " . number_format($row['jumlah'], 0, ',', '.') . "</td>";
              echo "<td>" . $row['status'] . "</td>";
              echo "<td class='action-buttons'>";
              if ($row['status'] === 'Pending') {
                echo "<form method='POST' style='display:inline;'>";
                echo "<input type='hidden' name='id' value='" . $row['IdPenarikan'] . "'>";
                echo "<button type='submit' name='action' value='terima' class='btn-action btn-terima'><i class='bi bi-check-lg'></i></button>";
                echo "<button type='submit' name='action' value='tolak' class='btn-action btn-tolak'><i class='bi bi-x-lg'></i></button>";
                echo "</form>";
              }
              echo "</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='6' class='text-center'>Tidak ada data penarikan</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <script>
    function toggleSubMenu(event) {
      const parentMenu = event.currentTarget;
      const submenuContainer = parentMenu.querySelector('.submenu-container');

      if (submenuContainer.hidden) {
        submenuContainer.hidden = false;
        parentMenu.querySelector('.custom-caret').classList.add('rotated');
      } else {
        submenuContainer.hidden = true;
        parentMenu.querySelector('.custom-caret').classList.remove('rotated');
      }
    }

    function toggleSidebar() {
      const sidebar = document.querySelector('.sidebar');
      const mainContent = document.querySelector('.main-content');
      const hamburgerMenu = document.querySelector('.hamburger-menu');
      sidebar.classList.toggle('active');
      mainContent.classList.toggle('sidebar-active');

      if (sidebar.classList.contains('active')) {
        mainContent.style.width = 'calc(100% - 250px)';
        mainContent.style.marginLeft = '250px';
        hamburgerMenu.style.left = '250px';
      } else {
        mainContent.style.width = '100%';
        mainContent.style.marginLeft = '0';
        hamburgerMenu.style.left = '20px';
      }
    }

    function toggleSubMenu(event) {
      const parentMenu = event.currentTarget;
      const subMenu = parentMenu.querySelector('.submenu-container');
      const caretIcon = parentMenu.querySelector('.custom-caret');

      subMenu.hidden = !subMenu.hidden;

      parentMenu.classList.toggle('open');

      if (parentMenu.classList.contains('open')) {
        caretIcon.style.transform = 'rotate(180deg)';
      } else {
        caretIcon.style.transform = 'rotate(0deg)';
      }
    }
  </script>
</body>

</html>