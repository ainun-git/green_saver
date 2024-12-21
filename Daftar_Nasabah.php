<?php
session_start();
include 'koneksi.php';

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  mysqli_begin_transaction($con);

  try {
    $query_hapus_transaksi = "DELETE FROM RiwayatTransaksi WHERE IdNasabah = ?";
    $stmt = mysqli_prepare($con, $query_hapus_transaksi);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);

    $query_hapus_penarikan = "DELETE FROM Penarikan WHERE IdNasabah = ?";
    $stmt = mysqli_prepare($con, $query_hapus_penarikan);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);

    $query_hapus_setoran = "DELETE FROM SetoranSampah WHERE IdNasabah = ?";
    $stmt = mysqli_prepare($con, $query_hapus_setoran);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);

    $query_hapus_nasabah = "DELETE FROM Nasabah WHERE IdNasabah = ?";
    $stmt = mysqli_prepare($con, $query_hapus_nasabah);
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);

    mysqli_commit($con);
    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Data nasabah berhasil dihapus!';
  } catch (Exception $e) {
    mysqli_rollback($con);
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Gagal menghapus data: ' . $e->getMessage();
  }

  header("Location: Daftar_Nasabah.php");
  exit();
}

function generateIdNasabah()
{
  global $koneksi;
  $query = "SELECT MAX(IdNasabah) AS max_id FROM Nasabah";
  $result = mysqli_query($koneksi, $query);
  $row = mysqli_fetch_assoc($result);
  $max_id = $row['max_id'];
  $next_id = (int) substr($max_id, 1) + 1;
  return 'N' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Nasabah</title>
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

  .nasabah-section {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .nasabah-section h2 {
    margin-top: 0;
    color: #567B65;
  }

  .search-box {
    margin-bottom: 20px;
  }

  .search-box input {
    width: 30%;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
  }

  .nasabah-table {
    width: 100%;
    border-collapse: collapse;
  }

  .nasabah-table th,
  .nasabah-table td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: center;
  }

  .nasabah-table th {
    text-align: center;
    background-color: #f2f2f2;
    color: #567B65;
  }

  .btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
  }

  .btn-danger {
    background-color: #dc3545;
    color: #fff;
  }

  .btn-edit {
    background-color: #af6b4c;
    color: #fff;
  }

  .btn-primary {
    background-color: #4CAF50;
    color: #fff;
  }

  .close-btn {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    top: 5px;

  }

  .close-btn:hover,
  .close-btn:focus {
    color: black;
    cursor: pointer;
  }

  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    overflow: auto;
  }

  .modal-content {
    background-color: #fff;
    margin: 20px auto;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    text-align: center;
    overflow-y: auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  }

  .modal-content bi-person-fill {
    font-size: 100px;
  }

  #editForm {
    text-align: left;
  }

  #editForm label {
    font-size: 10px;
    margin-bottom: 8px;
    display: block;
    text-align: left;
  }

  #editForm input {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
  }

  #editForm .modal-actions {
    display: flex;
    justify-content: right;
  }

  #editForm .modal-actions button {
    padding: 5px;
    margin: 5px;
    cursor: pointer;
    border: none;
    border-radius: 3px;
    font-size: 14px;
  }

  #detailsModal i {
    font-size: 40px;
    color: #4CAF50;
    margin-bottom: 10px;
  }

  #detailsModal i:hover {
    color: #FF5722;
    cursor: pointer;
    transform: scale(1.2);
    transition: transform 0.3s, color 0.3s;
  }

  #editForm .modal-actions button[type="button"]:first-child {
    background-color: #4CAF50;
    color: white;
  }

  #editForm .modal-actions button[type="button"]:last-child {
    background-color: #dc3545;
    color: white;
  }

  .close-btn {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    top: 5px;
    right: 5px;
    position: absolute;
  }

  .close-btn:hover,
  .close-btn:focus {
    color: black;
    cursor: pointer;
  }

  .modal-content {
    padding: 20px;
  }

  .alert-modal {
    display: none;
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    animation: slideIn 0.5s ease-out;
  }

  .alert-content {
    background-color: #4CAF50;
    color: white;
    padding: 15px 25px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .success-icon {
    font-size: 24px;
  }

  .success-animation {
    animation: fadeInOut 3s ease-in-out;
  }

  #successMessage {
    margin: 0;
    font-size: 16px;
    font-weight: 500;
  }

  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }

    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  @keyframes fadeInOut {
    0% {
      opacity: 0;
      transform: translateX(100%);
    }

    15% {
      opacity: 1;
      transform: translateX(0);
    }

    85% {
      opacity: 1;
      transform: translateX(0);
    }

    100% {
      opacity: 0;
      transform: translateX(100%);
    }
  }
</style>

<body>
  <div id="successModal" class="alert-modal">
    <div class="alert-content success-animation">
      <i class="bi bi-check-circle-fill success-icon"></i>
      <p id="successMessage">Data berhasil diupdate!</p>
    </div>
  </div>
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
        <p>Bank Sampah Cahaya Mandiri <i class="bi bi-person-fill"></i> </p>
      </div>
    </header>
    <div class="nasabah-section">
      <h2>Daftar Nasabah</h2>
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari nasabah..." onkeyup="searchNasabah()">
      </div>
      <table class="nasabah-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Nasabah</th>
            <th>Nomor Induk</th>
            <th>Alamat</th>
            <th>Username</th>
            <th>Saldo (Rp)</th>
            <th>Poin</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="nasabahTableBody">
          <?php
          $query = "SELECT * FROM Nasabah ORDER BY nama";
          $result = mysqli_query($con, $query);
          if (!$result) {
            die("Query error: " . mysqli_error($con));
          }

          $no = 1;
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nomorInduk']) . "</td>";
            echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>Rp " . number_format($row['saldo'], 0, ',', '.') . "</td>";
            echo "<td>" . $row['poin'] . "</td>";
            echo "<td>
                        <button class='btn btn-danger' onclick='deleteRow(\"" . $row['IdNasabah'] . "\")'><i class='bi bi-trash3'></i></button>
                        <button class='btn btn-edit' onclick='editRow(\"" . $row['IdNasabah'] . "\", \"" .
              htmlspecialchars($row['nama']) . "\", \"" .
              htmlspecialchars($row['nomorInduk']) . "\", \"" .
              htmlspecialchars($row['alamat']) . "\", \"" .
              htmlspecialchars($row['username']) . "\")'><i class='bi bi-pencil-square'></i></button>
                        <button class='btn btn-primary' onclick='viewDetails(\"" . $row['IdNasabah'] . "\", " .
              json_encode([
                'nama' => $row['nama'],
                'nomorInduk' => $row['nomorInduk'],
                'alamat' => $row['alamat'],
                'username' => $row['username'],
                'saldo' => $row['saldo'],
                'poin' => $row['poin']
              ]) . ")'><i class='bi bi-eye'></i></button>
                      </td>";
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeAddModal">&times;</span>
      <h2>Tambah/Edit Nasabah</h2>
      <form id="editForm" method="POST" action="proses_nasabah.php">
        <input type="hidden" id="editIdNasabah" name="IdNasabah">
        <label for="editNama">Nama Nasabah:</label>
        <input type="text" id="editNama" name="nama" required>

        <label for="editNomorInduk">Nomor Induk:</label>
        <input type="text" id="editNomorInduk" name="nomorInduk" required>

        <label for="editAlamat">Alamat:</label>
        <input type="text" id="editAlamat" name="alamat" required>

        <label for="editUsername">Username:</label>
        <input type="text" id="editUsername" name="username" required>

        <label for="editPassword">Password:</label>
        <input type="password" id="editPassword" name="password">

        <div class="modal-actions">
          <button type="submit">Simpan</button>
          <button type="button" onclick="closeEditModal()">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <div id="detailsModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" id="closeDetailsModal">&times;</span>
      <h2>Detail Nasabah</h2>
      <i class="bi bi-person-fill"></i>
      <div id="nasabahDetails"></div>
    </div>
  </div>

  <script src="Script_Sidebar.js"></script>
  <script>
    function searchNasabah() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toUpperCase();
      const table = document.querySelector('.nasabah-table');
      const rows = table.getElementsByTagName('tr');

      for (let i = 1; i < rows.length; i++) {
        const cols = rows[i].getElementsByTagName('td');
        let visible = false;

        for (let j = 0; j < cols.length; j++) {
          const cellText = cols[j].textContent || cols[j].innerText;
          if (cellText.toUpperCase().indexOf(filter) > -1) {
            visible = true;
            break;
          }
        }

        rows[i].style.display = visible ? '' : 'none';
      }
    }

    function deleteRow(idNasabah) {
      if (confirm('Apakah Anda yakin ingin menghapus nasabah ini? Semua data terkait nasabah ini juga akan dihapus.')) {
        fetch('Daftar_Nasabah.php?id=' + idNasabah, {
          method: 'GET'
        }).then(response => {
          location.reload();
        }).catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat menghapus data');
        });
      }
    }

    function editRow(idNasabah, nama, nomorInduk, alamat, username) {
      document.getElementById('editIdNasabah').value = idNasabah;
      document.getElementById('editNama').value = nama;
      document.getElementById('editNomorInduk').value = nomorInduk;
      document.getElementById('editAlamat').value = alamat;
      document.getElementById('editUsername').value = username;
      document.getElementById('editPassword').value = ''; 
      document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    function viewDetails(idNasabah, data) {
      const details = `
        <p><strong>Nama Nasabah:</strong> ${data.nama}</p>
        <p><strong>Nomor Induk:</strong> ${data.nomorInduk}</p>
        <p><strong>Alamat:</strong> ${data.alamat}</p>
        <p><strong>Username:</strong> ${data.username}</p>
        <p><strong>Saldo:</strong> Rp ${new Intl.NumberFormat('id-ID').format(data.saldo)}</p>
        <p><strong>Poin:</strong> ${data.poin}</p>
    `;
      document.getElementById('nasabahDetails').innerHTML = details;
      document.getElementById('detailsModal').style.display = 'block';
    }
    document.getElementById('closeDetailsModal').onclick = function() {
      document.getElementById('detailsModal').style.display = 'none';
    }

    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const nasabahSection = document.querySelector('.nasabah-section');

    hamburgerMenu.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      mainContent.classList.toggle('sidebar-active');
      nasabahSection.classList.toggle('sidebar-active');
      if (sidebar.classList.contains('active')) {
        hamburgerMenu.style.left = '250px';
      } else {
        hamburgerMenu.style.left = '20px';
      }
    });

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

    function showSuccessMessage(message) {
      const successModal = document.getElementById('successModal');
      const successMessage = document.getElementById('successMessage');
      successMessage.textContent = message || 'Data berhasil diupdate!';
      successModal.style.display = 'block';

      setTimeout(() => {
        successModal.style.display = 'none';
      }, 3000);
    }
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const status = urlParams.get('status');
      const message = urlParams.get('message');

      if (status === 'success') {
        showSuccessMessage(message);
        window.history.replaceState({}, document.title, window.location.pathname);
      }
    });
  </script>
</body>

</html>