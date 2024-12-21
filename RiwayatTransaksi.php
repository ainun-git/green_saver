<?php
include 'koneksi.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
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

    .penarikan {
      background-color: #FFFFFF;
      padding: 0 40px;
      width: 95%;

    }

    .penarikan h2 {
      padding-top: 1cm;
      padding-bottom: 1cm;
      color: #567B65;
      text-align: center;
    }

    .penarikan .transaksi {
      color: #567B65;
      display: flex;
      justify-content: center;

    }

    .table_tr {
      width: 98%;
      color: #567B65;
      border-collapse: collapse;
      margin: 25px auto;
      font-size: 16px;
      text-align: left;
    }

    .table_tr th:nth-child(1) {
      width: 25%;
    }

    .table_tr th:nth-child(2) {
      width: 25%;
    }

    .table_tr th:nth-child(3) {
      width: 25%;
    }

    .table_tr th:nth-child(4) {
      width: 25%;
    }

    .table_tr th {
      background-color: #f2f2f2;
      color: #567B65;
      font-weight: bold;
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }

    .table_tr td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }

    .table_tr tr:hover {
      background-color: #f5f5f5;
    }

    .table_tr tr:nth-child(even) {
      background-color: #fafafa;
    }

    tbody {
      color: black;

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
    <div class="penarikan">
      <h2>Riwayat Transaksi</h2>

      <table class="table_tr">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Jenis Transaksi</th>
            <th>Jumlah</th>
            <th>Tanggal Transaksi</th>
          </tr>
        </thead>
        <tbody id="nasabahTableBody">
          <?php
          $query = "SELECT n.nama, 
                          CASE 
                            WHEN rt.IdTransaksi IS NOT NULL THEN rt.jenisTransaksi
                            WHEN ss.IdSetoran IS NOT NULL THEN 'Setoran Sampah'
                          END as jenisTransaksi,
                          CASE 
                            WHEN rt.IdTransaksi IS NOT NULL THEN rt.jumlah
                            WHEN ss.IdSetoran IS NOT NULL THEN ss.totalHarga
                          END as jumlah,
                          CASE 
                            WHEN rt.IdTransaksi IS NOT NULL THEN rt.tanggalTransaksi
                            WHEN ss.IdSetoran IS NOT NULL THEN ss.tanggalSetor
                          END as tanggalTransaksi
                   FROM Nasabah n
                   LEFT JOIN RiwayatTransaksi rt ON n.IdNasabah = rt.IdNasabah
                   LEFT JOIN SetoranSampah ss ON n.IdNasabah = ss.IdNasabah
                   WHERE rt.IdTransaksi IS NOT NULL OR ss.IdSetoran IS NOT NULL
                   ORDER BY 
                     CASE 
                       WHEN rt.IdTransaksi IS NOT NULL THEN rt.tanggalTransaksi
                       WHEN ss.IdSetoran IS NOT NULL THEN ss.tanggalSetor
                     END DESC";

          $result = mysqli_query($con, $query);
          if (!$result) {
            die("Query gagal: " . mysqli_error($con));
          }

          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              echo "<tr>
                    <td>" . htmlspecialchars($row['nama']) . "</td>
                    <td>" . htmlspecialchars($row['jenisTransaksi']) . "</td>
                    <td>Rp " . number_format($row['jumlah'], 0, ',', '.') . "</td>
                    <td>" . date('d M Y', strtotime($row['tanggalTransaksi'])) . "</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='4'>Tidak ada data transaksi.</td></tr>";
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
        mainContent.style.marginLeft = '250px';
        hamburgerMenu.style.left = '250px';
      } else {
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