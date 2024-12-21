<?php
session_start();
include 'koneksi.php';

function generateIdSetoran($con)
{
  $query = "SELECT MAX(IdSetoran) AS max_id FROM SetoranSampah";
  $result = mysqli_query($con, $query);
  $data = mysqli_fetch_assoc($result);
  $max_id = $data['max_id'];

  if ($max_id === null) {
    return 'S001';
  }
  $nomor = (int)substr($max_id, 1);
  $nomor++;
  return 'S' . sprintf('%03d', $nomor);
}

function generateIdTransaksi($con)
{
  $query = "SELECT MAX(IdTransaksi) AS max_id FROM RiwayatTransaksi";
  $result = mysqli_query($con, $query);
  $data = mysqli_fetch_assoc($result);
  $max_id = $data['max_id'];

  if ($max_id === null) {
    return 'T001';
  }

  $nomor = (int)substr($max_id, 1);
  $nomor++;
  return 'T' . sprintf('%03d', $nomor);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aksi']) && $_POST['aksi'] == 'tambah_setoran') {
  $tanggal = mysqli_real_escape_string($con, $_POST['date']);
  $nama = mysqli_real_escape_string($con, $_POST['nasabah']);
  $kode_kategori = mysqli_real_escape_string($con, $_POST['kode_kategori']);
  $berat = floatval($_POST['berat_sampah']);

  if (empty($tanggal) || empty($nama) || empty($kode_kategori) || $berat <= 0) {
    $_SESSION['error'] = "Semua field harus diisi dengan benar.";
    header("Location: SetoranSampah.php");
    exit();
  }

  $query_nasabah = "SELECT IdNasabah, saldo, poin FROM Nasabah WHERE nama = '$nama'";
  $result_nasabah = mysqli_query($con, $query_nasabah);
  $nasabah = mysqli_fetch_assoc($result_nasabah);

  if ($nasabah) {
    $idNasabah = $nasabah['IdNasabah'];
    $saldo_sekarang = $nasabah['saldo'];
    $poin_sekarang = $nasabah['poin'];

    $query_harga = "SELECT harga FROM KategoriSampah WHERE kodeKategori = '$kode_kategori'";
    $result_harga = mysqli_query($con, $query_harga);
    $harga_kategori = mysqli_fetch_assoc($result_harga);

    if ($harga_kategori) {
      $total_harga = $berat * $harga_kategori['harga'];
      $idSetoran = generateIdSetoran($con);
      $idTransaksi = generateIdTransaksi($con);
      $poin_tambahan = floor($berat) * 5;
      $poin_baru = $poin_sekarang + $poin_tambahan;
      $saldo_baru = $saldo_sekarang + $total_harga;

      mysqli_begin_transaction($con);
      if (isset($_SESSION['pesan'])) {
        echo '<div class="alert-success">';
        echo '<i class="bi bi-check-circle-fill me-2"></i>' . $_SESSION['pesan'];
        echo '</div>';
        unset($_SESSION['pesan']);
      }
      try {
        $query_insert = "INSERT INTO SetoranSampah (IdSetoran, IdNasabah, kodeKategori, berat, totalHarga, tanggalSetor) 
                              VALUES ('$idSetoran', '$idNasabah', '$kode_kategori', $berat, $total_harga, '$tanggal')";

        if (!mysqli_query($con, $query_insert)) {
          throw new Exception("Gagal menambahkan setoran");
        }
        $query_insert_transaksi = "INSERT INTO RiwayatTransaksi (IdTransaksi, IdNasabah, jenisTransaksi, jumlah, tanggalTransaksi) 
                                         VALUES ('$idTransaksi', '$idNasabah', 'Setoran', $total_harga, '$tanggal')";

        if (!mysqli_query($con, $query_insert_transaksi)) {
          throw new Exception("Gagal menambahkan riwayat transaksi");
        }
        $query_update = "UPDATE Nasabah 
                              SET saldo = $saldo_baru, poin = $poin_baru 
                              WHERE IdNasabah = '$idNasabah'";

        if (!mysqli_query($con, $query_update)) {
          throw new Exception("Gagal mengupdate saldo dan poin");
        }

        mysqli_commit($con);
        $_SESSION['pesan'] = "Setoran sampah berhasil ditambahkan. Saldo dan poin telah diperbarui.";
      } catch (Exception $e) {
        mysqli_rollback($con);
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
      }
    } else {
      $_SESSION['error'] = "Harga kategori sampah tidak ditemukan.";
    }
  } else {
    $_SESSION['error'] = "Nasabah tidak ditemukan.";
  }
  header("Location: SetoranSampah.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['aksi']) && $_POST['aksi'] == 'hapus_setoran') {
  $idSetoran = mysqli_real_escape_string($con, $_POST['id_setoran']);

  $query_hapus = "DELETE FROM SetoranSampah WHERE IdSetoran = '$idSetoran'";
  if (mysqli_query($con, $query_hapus)) {
    $_SESSION['pesan'] = "Setoran sampah berhasil dihapus.";
  } else {
    $_SESSION['error'] = "Gagal menghapus setoran sampah: " . mysqli_error($con);
  }

  header("Location: SetoranSampah.php");
  exit();
}

$query_setoran = "
    SELECT s.IdSetoran, n.nama, k.kategori, s.berat, s.totalHarga, s.tanggalSetor 
    FROM SetoranSampah s
    JOIN Nasabah n ON s.IdNasabah = n.IdNasabah
    JOIN KategoriSampah k ON s.kodeKategori = k.kodeKategori
    ORDER BY s.tanggalSetor DESC";
$result_setoran = mysqli_query($con, $query_setoran);
$query_kategori = "SELECT kodeKategori, kategori FROM KategoriSampah";
$result_kategori = mysqli_query($con, $query_kategori);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setoran Sampah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    display: flex;
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
    color: #567B65;
    text-align: center;
    padding-bottom: 1cm;
    font-weight: bold;
  }

  .search-box-container {
    display: flex;
    align-items: center;
    gap: 25cm;
    margin-bottom: 20px;
  }

  .search-box input {
    width: 100%;
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
    padding: 6px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
    font-weight: bold;
  }

  .table .btn i {
    font-size: 18px;
    margin-right: 8px;
    font-weight: bold;
  }

  .search-box-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    width: 100%;
  }

  .search-box {
    width: 30%;
  }

  .search-box input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
  }

  .btn-primary {
    background-color: #4CAF50;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: bold;
  }

  .btn-primary:hover {
    background-color: #45a049;
  }

  .btn-primary i {
    font-size: 16px;
  }

  button.btn-primary:hover {
    background-color: #45a049;
  }

  input,
  select {
    width: 90%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 3px;
  }

  button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 10px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
  }

  button.reset {
    background-color: #f44336;
  }

  .modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
  }

  .modal .btn i {
    font-size: larger;
  }

  .modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border-radius: 8px;
    width: 80%;
    max-width: 400px;
    text-align: center;
  }

  .modal .btn i {
    font-size: larger;
    background-color: #567B65;
  }

  .close-btn {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    top: 5px;
    right: 15px;
  }

  .close-btn:hover,
  .close-btn:focus {
    color: black;
    cursor: pointer;
  }

  button {
    padding: 8px 8px;
    margin: 8px;
    font-size: 10px;
    border-radius: 4px;
    gap: 5px;
    font-weight: bold;

  }

  .btn-danger {
    background-color: red;
    color: white;
    border-radius: 5px;

  }

  .btn-secondary {
    background-color: gray;
    color: white;
    border: 5px;
  }

  .modal-content i {
    color: #dc3545;
    font-size: 50px;
  }

  .modal-content h3 {
    color: #567B65;
  }

  .alert-success {
    background-color: #D7F3E1;
    color: #567B65;
    padding: 15px;
    margin-top: 20px;
    margin-left: 40px;
    border-radius: 8px;
    text-align: center;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    animation: fadeInOut 3s forwards;
    border-left: 5px solid #4CAF50;
    font-size: 16px;
  }

  @keyframes fadeInOut {
    0% {
      opacity: 0;
      transform: translateY(-10px);
    }

    10% {
      opacity: 1;
      transform: translateY(0);
    }

    90% {
      opacity: 1;
      transform: translateY(0);
    }

    100% {
      opacity: 0;
      transform: translateY(-10px);
      display: none;
    }
  }
</style>

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
        <p>Bank Sampah Cahaya Mandiri <i class="bi bi-person-fill"></i></p>
      </div>
    </header>

    <?php
    if (isset($_SESSION['pesan'])) {
      echo '<div class="alert alert-success">' . $_SESSION['pesan'] . '</div>';
      unset($_SESSION['pesan']);
    }
    if (isset($_SESSION['error'])) {
      echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
      unset($_SESSION['error']);
    }
    ?>

    <?php
    $query_nasabah = "SELECT IdNasabah, nama FROM Nasabah ORDER BY nama";
    $result_nasabah = mysqli_query($con, $query_nasabah);
    ?>

    <div class="nasabah-section">
      <h2>Setoran Sampah</h2>
      <div class="search-box-container">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Cari nasabah..." onkeyup="searchTable()">
        </div>
        <button class="btn btn-primary" id="tambahBtn"><i class="bi bi-plus-circle"></i> Tambah</button>
      </div>
      <table class="nasabah-table" id="setoranTable">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Nasabah</th>
            <th>Kategori Sampah</th>
            <th>Berat (Kg)</th>
            <th>Total Harga</th>
            <th>Tanggal Setor</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          while ($row = mysqli_fetch_assoc($result_setoran)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['nama']) ?></td>
              <td><?= htmlspecialchars($row['kategori']) ?></td>
              <td><?= number_format($row['berat'], 2) ?></td>
              <td>Rp <?= number_format($row['totalHarga'], 0, ',', '.') ?></td>
              <td><?= date('d M Y', strtotime($row['tanggalSetor'])) ?></td>
              <td>
                <button class="btn btn-danger delete-btn" data-id="<?= $row['IdSetoran'] ?>">
                  <i class="bi bi-trash3"></i>Hapus
                </button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal" id="addModal">
    <div class="modal-content">
      <span class="close-btn" id="closeAddModal">&times;</span>
      <h3>Tambah Setoran Sampah</h3>
      <form method="POST">
        <input type="hidden" name="aksi" value="tambah_setoran">
        <input type="date" name="date" id="date" required>
        <select name="nasabah" id="nasabah" required>
          <option value="">Pilih Nasabah</option>
          <?php while ($row_nasabah = mysqli_fetch_assoc($result_nasabah)) { ?>
            <option value="<?php echo $row_nasabah['nama']; ?>">
              <?php echo $row_nasabah['nama']; ?>
            </option>
          <?php } ?>
        </select>
        <select name="kategori_sampah" id="kategori-sampah" required onchange="updateKodeKategori()">
          <option value="">Pilih Kategori Sampah</option>
          <?php
          $query_kategori = "SELECT DISTINCT kategori FROM KategoriSampah";
          $result_kategori = mysqli_query($con, $query_kategori);
          while ($kategori = mysqli_fetch_assoc($result_kategori)) { ?>
            <option value="<?php echo strtolower($kategori['kategori']); ?>">
              <?php echo $kategori['kategori']; ?>
            </option>
          <?php } ?>
        </select>
        <select name="kode_kategori" id="kode-kategori" required>
          <option value="">Pilih Kode Kategori</option>
        </select>
        <input type="text" name="berat_sampah" id="berat-sampah" placeholder="Berat Sampah (Kg)" required>
        <div>
          <button type="reset" class="reset">Reset</button>
          <button type="submit">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal" id="deleteModal">
    <div class="modal-content">
      <span class="close-btn" id="closeModal">&times;</span>
      <i class="bi bi-exclamation-circle"></i>
      <p>Apakah Anda yakin ingin menghapus?</p>
      <form method="POST">
        <input type="hidden" name="aksi" value="hapus_setoran">
        <input type="hidden" name="id_setoran" id="id_setoran_hapus">
        <button type="submit" class="btn btn-danger">Hapus</button>
        <button type="button" class="btn btn-secondary" id="cancelDelete">Batal</button>
      </form>
    </div>
  </div>

  <script>
    function updateKodeKategori() {
      const kategoriSampah = document.getElementById('kategori-sampah').value;
      const kodeKategoriSelect = document.getElementById('kode-kategori');
      kodeKategoriSelect.innerHTML = '<option value="">Pilih Kode Kategori</option>';

      if (kategoriSampah) {
        fetch(`get_kode_kategori.php?kategori=${kategoriSampah}`)
          .then(response => response.json())
          .then(data => {
            data.forEach(item => {
              const option = document.createElement('option');
              option.value = item.kodeKategori;
              option.textContent = `${item.kodeKategori} - ${item.namaKategori}`;
              kodeKategoriSelect.appendChild(option);
            });
          });
      }
    }

    function searchTable() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("searchInput");
      filter = input.value.toUpperCase();
      table = document.getElementById("setoranTable");
      tr = table.getElementsByTagName("tr");

      for (i = 1; i < tr.length; i++) {
        var found = false;
        for (var j = 1; j < 6; j++) {
          td = tr[i].getElementsByTagName("td")[j];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              found = true;
              break;
            }
          }
        }
        tr[i].style.display = found ? "" : "none";
      }
    }

    const addModal = document.getElementById("addModal");
    const tambahBtn = document.querySelector(".btn-primary");
    const closeAddModalButton = document.getElementById("closeAddModal");

    tambahBtn.addEventListener("click", () => {
      addModal.style.display = "flex";
    });

    closeAddModalButton.addEventListener("click", () => {
      addModal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
      if (event.target == addModal) {
        addModal.style.display = "none";
      }
    });
    const deleteModal = document.getElementById('deleteModal');
    const closeModalButton = document.getElementById('closeModal');
    const cancelDeleteButton = document.getElementById('cancelDelete');
    const idSetoranHapus = document.getElementById('id_setoran_hapus');

    document.addEventListener('DOMContentLoaded', function() {
      const deleteButtons = document.querySelectorAll('.delete-btn');
      deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
          const idSetoran = button.getAttribute('data-id');
          idSetoranHapus.value = idSetoran;
          deleteModal.style.display = "flex";
        });
      });

      cancelDeleteButton.addEventListener('click', () => {
        deleteModal.style.display = "none";
      });

      closeModalButton.addEventListener('click', () => {
        deleteModal.style.display = "none";
      });
    });

    window.addEventListener('click', (event) => {
      if (event.target == deleteModal) {
        deleteModal.style.display = "none";
      }
    });

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
      caretIcon.style.transform = parentMenu.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
    }
  </script>
</body>

</html>