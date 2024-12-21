<?php
session_start();
include 'koneksi.php';

$query_pengurus = "SELECT IdPengurus, namaPengurus FROM Pengurus ORDER BY namaPengurus";
$result_pengurus = mysqli_query($con, $query_pengurus);

function generateIdPenjualan($con)
{
  $query = "SELECT MAX(IdPenjualan) AS max_id FROM PenjualanSampah";
  $result = mysqli_query($con, $query);
  $data = mysqli_fetch_assoc($result);
  $max_id = $data['max_id'];

  if ($max_id) {
    $no_urut = (int)substr($max_id, 2) + 1;
  } else {
    $no_urut = 1;
  }
  return 'PJ' . sprintf('%03d', $no_urut);
}

if (isset($_GET['hapus'])) {
  $idPenjualan = $_GET['hapus'];
  $queryHapus = "DELETE FROM PenjualanSampah WHERE IdPenjualan = '$idPenjualan'";

  if (mysqli_query($con, $queryHapus)) {
    $_SESSION['pesan'] = "Berhasil menghapus data penjualan";
  } else {
    $_SESSION['error'] = "Gagal menghapus data: " . mysqli_error($con);
  }

  header("Location: penjualan.php");
  exit();
}

$dataEdit = null;
if (isset($_GET['edit'])) {
  $idPenjualan = mysqli_real_escape_string($con, $_GET['edit']);
  $queryEdit = "SELECT p.*, pg.namaPengurus, k.kategori, k.namaKategori
                FROM PenjualanSampah p
                JOIN Pengurus pg ON p.IdPengurus = pg.IdPengurus
                JOIN KategoriSampah k ON p.kodeKategori = k.kodeKategori
                WHERE p.IdPenjualan = '$idPenjualan'";
  $resultEdit = mysqli_query($con, $queryEdit);

  if ($resultEdit && mysqli_num_rows($resultEdit) > 0) {
    $dataEdit = mysqli_fetch_assoc($resultEdit);

    if (
      !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {
      header('Content-Type: application/json');
      // Prepare response data
      $response = array(
        'IdPenjualan' => $dataEdit['IdPenjualan'],
        'tanggalPenjualan' => $dataEdit['tanggalPenjualan'],
        'namaPengurus' => $dataEdit['namaPengurus'],
        'kategori' => $dataEdit['kategori'],
        'kodeKategori' => $dataEdit['kodeKategori'],
        'totalBerat' => $dataEdit['totalBerat'],
        'totalHarga' => $dataEdit['totalHarga'],
        'keterangan' => $dataEdit['keterangan']
      );
      echo json_encode($response);
      exit;
    }
  } else {
    if (
      !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {
      header('HTTP/1.1 404 Not Found');
      echo json_encode(['error' => 'Data tidak ditemukan']);
      exit;
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $namaPengurus = mysqli_real_escape_string($con, $_POST['pengurus']);
  $tanggalPenjualan = mysqli_real_escape_string($con, $_POST['date']);
  $kategori_sampah = mysqli_real_escape_string($con, $_POST['kategori_sampah']);
  $kode_kategori = mysqli_real_escape_string($con, $_POST['kode_kategori']);
  $berat = floatval($_POST['berat_sampah']);
  $keterangan = mysqli_real_escape_string($con, $_POST['keterangan']);

  $queryPengurus = "SELECT IdPengurus FROM Pengurus WHERE namaPengurus = '$namaPengurus'";
  $resultPengurus = mysqli_query($con, $queryPengurus);
  $dataPengurus = mysqli_fetch_assoc($resultPengurus);
  $idPengurus = $dataPengurus['IdPengurus'];

  if ($dataPengurus) {
    $query_harga = "SELECT harga FROM KategoriSampah WHERE kodeKategori = '$kode_kategori'";
    $result_harga = mysqli_query($con, $query_harga);
    $harga_kategori = mysqli_fetch_assoc($result_harga);

    if ($harga_kategori) {
      $total_harga = $berat * $harga_kategori['harga'];
      $idPenjualan = generateIdPenjualan($con);

      if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $idPenjualan = mysqli_real_escape_string($con, $_POST['id_penjualan']);
        $queryUpdate = "UPDATE PenjualanSampah SET 
                  IdPengurus = '$idPengurus',
                  tanggalPenjualan = '$tanggalPenjualan',
                  kodeKategori = '$kode_kategori',
                  totalBerat = '$berat',
                  totalHarga = '$total_harga',
                  keterangan = '$keterangan'
                  WHERE IdPenjualan = '$idPenjualan'";

        if (mysqli_query($con, $queryUpdate)) {
          $_SESSION['pesan'] = "Berhasil mengubah data penjualan";
        } else {
          $_SESSION['error'] = "Gagal mengubah data: " . mysqli_error($con);
        }
      } else {
        $queryTambah = "INSERT INTO PenjualanSampah 
                  (IdPenjualan, IdPengurus, tanggalPenjualan,
                  kodeKategori, totalBerat, totalHarga, keterangan)
                  VALUES 
                  ('$idPenjualan', '$idPengurus', '$tanggalPenjualan',
                  '$kode_kategori', '$berat', '$total_harga', '$keterangan')";

        if (mysqli_query($con, $queryTambah)) {
          $_SESSION['pesan'] = "Berhasil menambah data penjualan";
        } else {
          $_SESSION['error'] = "Gagal menambah data: " . mysqli_error($con);
        }
      }
    } else {
      $_SESSION['error'] = "Harga kategori sampah tidak ditemukan";
    }
  } else {
    $_SESSION['error'] = "Pengurus tidak ditemukan";
  }

  header("Location: penjualan.php");
  exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $idPenjualan = generateIdPenjualan($con);
  $namaPengurus = $_POST['nasabah'];
  $tanggalPenjualan = $_POST['date'];
  $kategoriSampah = $_POST['kategori_sampah'];
  $jenisSampah = $_POST['jenis_sampah'];
  $totalBerat = $_POST['berat_sampah'];
  $totalHarga = $_POST['harga_sampah'];
  $keterangan = $_POST['keterangan'];

  $queryPengurus = "SELECT IdPengurus FROM Pengurus WHERE namaPengurus = '$namaPengurus'";
  $resultPengurus = mysqli_query($con, $queryPengurus);
  $dataPengurus = mysqli_fetch_assoc($resultPengurus);
  $idPengurus = $dataPengurus['IdPengurus'];

  $queryTambah = "INSERT INTO PenjualanSampah 
    (IdPenjualan, IdPengurus, namaPengurus, tanggalPenjualan, 
    jenisSampah, totalBerat, totalHarga, keterangan) 
    VALUES 
    ('$idPenjualan', '$idPengurus', '$namaPengurus', '$tanggalPenjualan', 
    '$jenisSampah', '$totalBerat', '$totalHarga', '$keterangan')";
  if (isset($_SESSION['pesan'])) {
    echo '<div class="alert-success">';
    echo '<i class="bi bi-check-circle-fill me-2"></i>' . $_SESSION['pesan'];
    echo '</div>';
    unset($_SESSION['pesan']);
  }

  if (mysqli_query($con, $queryTambah)) {
    $_SESSION['pesan'] = "Berhasil menambah data penjualan";
  } else {
    $_SESSION['error'] = "Gagal menambah data: " . mysqli_error($con);
  }

  header("Location: penjualan.php");
  exit();
}

$query = "SELECT p.IdPenjualan, pg.namaPengurus, p.tanggalPenjualan, 
          k.kategori, k.namaKategori, p.totalBerat, p.totalHarga, p.keterangan 
          FROM PenjualanSampah p
          JOIN Pengurus pg ON p.IdPengurus = pg.IdPengurus
          JOIN KategoriSampah k ON p.kodeKategori = k.kodeKategori
          ORDER BY p.tanggalPenjualan DESC";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Penjualan Sampah</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
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
    color: #567B65;
    text-align: center;
    padding-bottom: 1cm;
    font-weight: bold;
  }

  .btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    cursor: pointer;
  }

  button.btn-primary {
    position: fixed;
    top: 150px;
    right: 40px;
    margin-bottom: 40px;
    padding-left: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;

  }

  button.btn-primary:hover {
    background-color: #45a049;
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
    border-radius: 10px;

  }

  .btn-secondary {
    background-color: gray;
    color: white;
    border: none;
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

    <?php if (isset($_SESSION['pesan'])): ?>
      <div class="alert alert-success">
        <?= $_SESSION['pesan'] ?>
        <?php unset($_SESSION['pesan']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger">
        <?= $_SESSION['error'] ?>
        <?php unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <div class="nasabah-section">
      <h2>Penjualan</h2>

      <div class="btn1">
        <button class="btn btn-primary" onclick="bukaPengaturan()">
          <i class="bi bi-plus-circle"></i>Tambah
        </button>
      </div>

      <table class="nasabah-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Pengurus</th>
            <th>Tanggal Penjualan</th>
            <th>Kategori Sampah</th>
            <th>Berat Sampah(Kg)</th>
            <th>Total Harga(Rp)</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $no = 1;
          while ($row = mysqli_fetch_assoc($result)):
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= $row['namaPengurus'] ?></td>
              <td><?= $row['tanggalPenjualan'] ?></td>
              <td><?= $row['kategori'] ?> - <?= $row['namaKategori'] ?></td>
              <td><?= $row['totalBerat'] ?></td>
              <td>Rp. <?= number_format($row['totalHarga'], 0, ',', '.') ?></td>
              <td><?= $row['keterangan'] ?></td>
              <td>
                <button class="btn btn-secondary" onclick="editPenjualan('<?= $row['IdPenjualan'] ?>')">Edit</button>
                <button class="btn btn-danger" onclick="konfirmasiHapus('<?= $row['IdPenjualan'] ?>')">Hapus</button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal" id="addModal">
    <div class="modal-content">
      <span class="close-btn" onclick="tutupPengaturan()">&times;</span>
      <h3>Tambah Penjualan Sampah</h3>

      <form method="POST" action="">
        <input type="hidden" name="action" value="<?= isset($dataEdit) ? 'update' : 'add' ?>">
        <?php if (isset($dataEdit)): ?>
          <input type="hidden" name="id_penjualan" value="<?= $dataEdit['IdPenjualan'] ?>">
        <?php endif; ?>

        <input type="date" name="date" id="date" value="<?= isset($dataEdit) ? $dataEdit['tanggalPenjualan'] : '' ?>" required>

        <select name="pengurus" id="pengurus" required>
          <option value="">Pilih Pengurus</option>
          <?php
          mysqli_data_seek($result_pengurus, 0);
          while ($row_pengurus = mysqli_fetch_assoc($result_pengurus)) {
          ?>
            <option value="<?php echo $row_pengurus['namaPengurus']; ?>"
              <?= (isset($dataEdit) && $dataEdit['namaPengurus'] == $row_pengurus['namaPengurus']) ? 'selected' : '' ?>>
              <?php echo $row_pengurus['namaPengurus']; ?>
            </option>
          <?php } ?>
        </select>

        <select name="kategori_sampah" id="kategori-sampah" required onchange="updateKodeKategori()">
          <option value="">Pilih Kategori Sampah</option>
          <?php
          $query_kategori = "SELECT DISTINCT kategori FROM KategoriSampah";
          $result_kategori = mysqli_query($con, $query_kategori);
          while ($kategori = mysqli_fetch_assoc($result_kategori)) {
          ?>
            <option value="<?php echo strtolower($kategori['kategori']); ?>"
              <?= (isset($dataEdit) && strtolower($dataEdit['kategori']) == strtolower($kategori['kategori'])) ? 'selected' : '' ?>>
              <?php echo $kategori['kategori']; ?>
            </option>
          <?php } ?>
        </select>

        <select name="kode_kategori" id="kode-kategori" required>
          <option value="">Pilih Kode Kategori</option>
        </select>

        <input type="number" step="0.01" name="berat_sampah" id="berat-sampah" value="<?= isset($dataEdit) ? $dataEdit['totalBerat'] : '' ?>" placeholder="Berat Sampah (Kg)" required>

        <input type="text" name="keterangan" id="keterangan" value="<?= isset($dataEdit) ? $dataEdit['keterangan'] : '' ?>" placeholder="Keterangan" required>

        <div>
          <button type="reset" class="reset">Reset</button>
          <button type="submit">
            <?= isset($dataEdit) ? 'Update' : 'Simpan' ?>
          </button>
        </div>
      </form>
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

    function bukaPengaturan() {
      document.getElementById('addModal').style.display = 'flex';
      const editModal = document.getElementById('editModal');
      if (editModal) {
        editModal.style.display = 'none';
      }
    }

    function tutupPengaturan() {
      document.getElementById('addModal').style.display = 'none';
    }

    function konfirmasiHapus(idPenjualan) {
      if (confirm('Apakah Anda yakin ingin menghapus data penjualan ini?')) {
        window.location.href = `penjualan.php?hapus=${idPenjualan}`;
      }
    }

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

    function editPenjualan(idPenjualan) {
      fetch('penjualan.php?edit=' + idPenjualan, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        .then(response => {
          if (!response.ok) {
            throw new Error('Data tidak ditemukan');
          }
          return response.json();
        })
        .then(data => {
          console.log('Data from server:', data);

          document.getElementById('date').value = data.tanggalPenjualan;
          document.getElementById('pengurus').value = data.namaPengurus;

          const kategoriSelect = document.getElementById('kategori-sampah');
          if (data.kategori) {
            kategoriSelect.value = data.kategori.toLowerCase();
            kategoriSelect.dispatchEvent(new Event('change'));

            setTimeout(() => {
              const kodeKategoriSelect = document.getElementById('kode-kategori');
              if (kodeKategoriSelect) {
                kodeKategoriSelect.value = data.kodeKategori;
              }
            }, 500);
          }

          document.getElementById('berat-sampah').value = data.totalBerat;
          document.getElementById('keterangan').value = data.keterangan;

          const form = document.querySelector('#addModal form');
          let hiddenInput = form.querySelector('input[name="id_penjualan"]');
          if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'id_penjualan';
            form.appendChild(hiddenInput);
          }
          hiddenInput.value = idPenjualan;

          let actionInput = form.querySelector('input[name="action"]');
          if (!actionInput) {
            actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            form.appendChild(actionInput);
          }
          actionInput.value = 'update';
          document.querySelector('#addModal h3').textContent = 'Edit Penjualan Sampah';
          document.querySelector('#addModal button[type="submit"]').textContent = 'Update';
          document.getElementById('addModal').style.display = 'flex';
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat mengambil data: ' + error.message);
        });
    }

    function tutupEdit() {
      <?php if ($dataEdit): ?>
        window.location.href = 'penjualan.php';
      <?php endif; ?>
    }

    window.addEventListener('click', function(event) {
      const modal = document.getElementById('addModal');
      const editModal = document.getElementById('editModal');

      if (modal && event.target == modal) {
        modal.style.display = 'none';
      }

      <?php if ($dataEdit): ?>
        if (editModal && event.target == editModal) {
          window.location.href = 'penjualan.php';
        }
      <?php endif; ?>
    });
  </script>
</body>

</html>