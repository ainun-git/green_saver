<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['IdPengurus'])) {
  header("Location: kategori.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Harga Sampah Anorganik</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      background-color: #f9f9f9;
    }

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
      padding: 20px;
      box-sizing: border-box;
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

    .tab h3 {
      text-align: center;
      color: #567B65;
      margin-left: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 0;
    }

    th,
    td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #f4f4f4;
      color: #333;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .category {
      background-color: #cfe2f3;
      font-weight: bold;
      text-align: center;
    }
  </style>
</head>

<body>
  <button class="hamburger-menu" onclick="toggleSidebar()">&#9776;</button>
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
        <p>Bank Sampah Cahaya Mandiri<i class="bi bi-person-fill"></i></p>
      </div>
    </header>
    <div class="tab">
      <h3>Daftar Kategori Sampah</h3>
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Kelompok / Kategori</th>
            <th>Kode</th>
            <th>Contoh Barang/Produk</th>
            <th>Harga (Rp/Kg/Pcs)</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $kategoriSaatIni = '';
          $query = "SELECT * FROM KategoriSampah ORDER BY kategori, kodeKategori";
          $result = mysqli_query($con, $query);
          $nomor = 1;

          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              if ($kategoriSaatIni != $row['kategori']) {
                echo "<tr class='category'>";
                echo "<td colspan='5'>" . strtoupper($row['kategori']) . "</td>";
                echo "</tr>";
                $kategoriSaatIni = $row['kategori'];
              }
              echo "<tr>";
              echo "<td>" . $nomor . "</td>";
              echo "<td>" . $row['namaKategori'] . "</td>";
              echo "<td>" . $row['kodeKategori'] . "</td>";
              echo "<td>" . $row['contohBarang'] . "</td>";
              echo "<td>" . number_format($row['harga'], 0, ',', '.') . "</td>";
              echo "</tr>";

              $nomor++;
            }
          } else {
            echo "<tr><td colspan='5' style='text-align:center;'>Tidak ada data kategori sampah</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
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