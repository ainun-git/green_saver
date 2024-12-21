<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['IdPengurus'])) {
  header("Location: dashboardPengurus.php");
  exit();
}
$sql_setoran = "SELECT SUM(berat) AS kg FROM setoransampah";
$result_setoran = $con->query($sql_setoran);
$setoransampah = $result_setoran->num_rows > 0 ? $result_setoran->fetch_assoc()['kg'] : 0;

$sql_penjualan = "SELECT SUM(totalHarga) AS rp FROM penjualansampah";
$result_penjualan = $con->query($sql_penjualan);
$penjualansampah = $result_penjualan->num_rows > 0 ? $result_penjualan->fetch_assoc()['rp'] : 0;

$salesData = array_fill(0, 12, 0);
$depositData = array_fill(0, 12, 0);

$queryPenjualan = "SELECT MONTH(tanggalPenjualan) AS bulan, SUM(totalHarga) AS totalPenjualan 
                   FROM penjualansampah 
                   GROUP BY MONTH(tanggalPenjualan)";
$resultPenjualan = $con->query($queryPenjualan);

while ($row = $resultPenjualan->fetch_assoc()) {
  $salesData[$row['bulan'] - 1] = (float)$row['totalPenjualan'];
}

$querySetoran = "SELECT MONTH(tanggalSetor) AS bulan, SUM(berat) AS totalBerat 
                 FROM setoransampah 
                 GROUP BY MONTH(tanggalSetor)";
$resultSetoran = $con->query($querySetoran);

while ($row = $resultSetoran->fetch_assoc()) {
  $depositData[$row['bulan'] - 1] = (float)$row['totalBerat'];
}
$queryNasabahTeraktif = "
SELECT nasabah.IdNasabah, nasabah.nama, SUM(setoransampah.berat) AS totalSetoran
FROM setoransampah
JOIN nasabah ON setoransampah.IdNasabah = nasabah.IdNasabah
GROUP BY nasabah.IdNasabah
ORDER BY totalSetoran DESC
LIMIT 10";

if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

$resultNasabahTeraktif = $con->query($queryNasabahTeraktif);

if (!$resultNasabahTeraktif) {
  die("Query failed: " . $con->error);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
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
    display: flex;
    padding: 20px;
    box-sizing: border-box;
    border-radius: 50px;


  }


  .main-content header {
    font-size: 20px;
    background-color: #FFFFFF;
    color: #567B65;
    text-align: right;
    position: absolute;
    top: 0;
    right: 0;
  }

  .dashboard-overview {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
    margin-top: 30px;
    justify-items: center;
  }

  .main-content .dashboard-overview .card {
    flex: 1;
    display: block;
    width: 93%;
    gap: 4px;
    background-color: #567B65;
    padding: 10px;
    padding-bottom: 50px;
    margin-left: 35px;
    border-radius: 15px;
    white-space: nowrap;

  }

  .card {
    width: 100%;
    margin-left: 40px;
  }

  .card h3 {
    margin-top: 1px;
    color: #FFFFFF;
  }

  .card p {
    font-size: larger;
    color: #FFFFFF;
    text-align: left;
    margin-bottom: auto;
  }

  .charts {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 48%;
    margin-left: 35px;
    margin-bottom: 20px;
  }

  .chart {
    flex: 1;
    display: flex;
    width: 18cm;
    padding-bottom: 100px;
    background-color: #FFFFFF;
    padding: 5px;
    border-radius: 8px;
    text-align: left;
    border: 2px solid #a7a6a6;

  }

  .chart h3 {
    margin: 0;
    margin-bottom: 10px;
    color: #567B65;
  }

  .charts .chart canvas {
    display: block;
    box-sizing: border-box;
    height: 340px;
    width: 600px;
  }

  .dasboard-2 {
    flex-direction: column;
    padding-right: 0;
  }

  .calendar {
    font-size: 15px;
    background-color: #fff9f9;
    border-radius: 1px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-top: 1cm;
    margin: 1px 1px 1px 1px;
    padding: 10px 10px;
  }

  .btn {
    display: flex;
    align-items: center;
    padding: 15px;
    background-color: #fff9f9;
    color: #567B65;
  }

  button {
    background: none;
    border: none;
    color: #567B65;
    font-size: 1.5rem;
    cursor: pointer;
  }

  button:hover {
    opacity: 0.8;
  }

  .button .next {
    padding-left: 0;
  }

  .calendar-days {
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    padding: 1px;
    background-color: #f0f0f0;
    font-weight: bold;
  }

  .calendar-dates {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    padding: 100px;
  }

  .day,
  .date {
    padding: 15px;
    text-align: center;
  }

  .date {
    cursor: pointer;
    transition: background-color 0.3s;
  }

  .date:hover {
    background-color: #4CAF50;
    color: white;
  }

  .date.disabled {
    color: #ddd;
    cursor: not-allowed;
  }

  header h2 {
    font-size: 1.2rem;
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

  .calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    padding: 2px;
    background-color: #f0f0f0;
    font-weight: bold;
  }

  .calendar-dates {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    padding: 10px;
  }

  .day,
  .date {
    padding: 15px;
    text-align: center;
  }

  .date {
    cursor: pointer;
    transition: background-color 0.3s;
  }

  .date:hover {
    background-color: #4CAF50;
    color: white;
  }

  .date.disabled {
    color: #ddd;
    cursor: not-allowed;
  }

  .today {
    background-color: #4CAF50;
    color: white;
    border-radius: 50%;
    font-weight: bold;
  }

  .nasabah-container {
    width: 80%;
    max-width: 600px;
    margin: 10px auto;
    font-family: Arial, sans-serif;
    color: #333;
  }

  .nasabah-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }

  .nasabah-header h4 {
    font-size: 20px;
    font-weight: bold;
    color: #006400;
  }

  .nasabah-header a {
    text-decoration: none;
    color: #006400;
    font-size: 16px;
    font-weight: bold;
  }

  .nasabah-header a:hover {
    text-decoration: underline;
  }

  .nasabah-list {
    border-top: 1px solid #ccc;
  }

  .nasabah-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #ccc;
  }

  .nasabah-icon {
    font-size: 24px;
    margin-right: 15px;
    color: #567B65;
  }

  .nasabah-info {
    display: flex;
    flex-direction: column;
  }

  .nasabah-name {
    text-align: left;
    font-size: 15px;
    font-weight: bold;
    color: #567B65;
    margin: 0;
  }

  .nasabah-setoran {
    font-size: 9px;
    margin: 0;
    color: #666;
  }
</style>

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
  <div class="main-content" id="mainContent">
    <header>
      <p>Bank Sampah Cahaya Mandiri <i class="bi bi-person-fill"></i></p>
      <div class="dashboard-2">
        <div class="calendar">
          <div class="btn">
            <h2 id="month-year"></h2>
            <button class="prev" onclick="changeMonth(-1)">&#10094;</button>
            <button class="next" onclick="changeMonth(1)">&#10095;</button>
          </div>

          <div class="calendar-days">
            <div class="day">Min</div>
            <div class="day">Sen</div>
            <div class="day">Sel</div>
            <div class="day">Rab</div>
            <div class="day">Kam</div>
            <div class="day">Jum</div>
            <div class="day">Sab</div>
          </div>

          <div class="calendar-dates" id="dates"></div>
        </div>

        <div class="nasabah-container">
          <div class="nasabah-header">
            <h4>Nasabah Teraktif</h4>
            <a href="#">Lihat Semua</a>
          </div>
          <div class="nasabah-list">
            <?php
            while ($row = $resultNasabahTeraktif->fetch_assoc()) {
              echo '<div class="nasabah-item">';
              echo '  <div class="nasabah-icon">👤</div>';
              echo '  <div class="nasabah-info">';
              echo '    <p class="nasabah-name">' . htmlspecialchars($row['nama']) . '</p>';
              echo '    <p class="nasabah-setoran">Setoran: ' . number_format($row['totalSetoran'], 2) . ' kg</p>';
              echo '  </div>';
              echo '</div>';
            }
            ?>
          </div>
        </div>
      </div>
    </header>

    <div class="dashboard-overview">
      <div class="card">
        <h3>Penjualan Sampah</h3>
        <p>Rp <?php echo number_format($penjualansampah, 2); ?></p>
      </div>
      <div class="card">
        <h3>Setoran Sampah</h3>
        <p><?php echo number_format($setoransampah, 2); ?> kg</p>
      </div>

      <div class="charts">
        <div class="chart">
          <canvas id="salesChart"></canvas>
        </div>
        <div class="chart">
          <canvas id="depositChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <script src="ScriptKal.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
  <script>
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const salesData = <?php echo json_encode($salesData); ?>;
    const depositData = <?php echo json_encode($depositData); ?>;

    // Grafik Penjualan
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Penjualan Sampah (Rp)',
          data: salesData,
          borderColor: '#4CAF50',
          backgroundColor: 'rgba(76, 175, 80, 0.2)',
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                let value = context.raw.toLocaleString('id-ID', {
                  style: 'currency',
                  currency: 'IDR'
                });
                return value;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Grafik Setoran
    const depositCtx = document.getElementById('depositChart').getContext('2d');
    new Chart(depositCtx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Setoran Sampah (kg)',
          data: depositData,
          borderColor: '#FF9800',
          backgroundColor: 'rgba(171, 207, 10, 0.45)',
          fill: true
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</body>
</html>