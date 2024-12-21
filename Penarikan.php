<?php
include "koneksi.php";

session_start();
if (!isset($_SESSION['IdNasabah'])) {
    header("Location: Penarikan.php");
    exit();
}
$message = '';

$idNasabah = $_SESSION['IdNasabah'];
$querySaldoPoin = "SELECT saldo, poin FROM Nasabah WHERE IdNasabah = '$idNasabah'";
$resultSaldoPoin = mysqli_query($con, $querySaldoPoin);
$dataNasabah = mysqli_fetch_assoc($resultSaldoPoin);

$saldo = $dataNasabah['saldo'];
$poin = $dataNasabah['poin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['jenis_penarikan']) && $_POST['jenis_penarikan'] === 'saldo') {
        $tanggal = $_POST['tanggalSaldo'];
        $jumlah = $_POST['jumlahPenarikan'];

        if (empty($tanggal) || empty($jumlah)) {
            $message = "Harap isi semua field!";
        } elseif ($jumlah <= 0) {
            $message = "Jumlah penarikan harus lebih dari 0!";
        } else {
            $idNasabah = $_SESSION['IdNasabah'];
            $nama = $_SESSION['nama'];
            $queryLastId = "SELECT MAX(IdPenarikan) as last_id FROM Penarikan";
            $resultLastId = mysqli_query($con, $queryLastId);
            $rowLastId = mysqli_fetch_assoc($resultLastId);
            $lastId = $rowLastId['last_id'];
            $newId = 'PR' . sprintf('%03d', (int)substr($lastId, 2) + 1);
            $queryLastTransaksiId = "SELECT MAX(IdTransaksi) as last_transaksi_id FROM RiwayatTransaksi";
            $resultLastTransaksiId = mysqli_query($con, $queryLastTransaksiId);
            $rowLastTransaksiId = mysqli_fetch_assoc($resultLastTransaksiId);
            $lastTransaksiId = $rowLastTransaksiId['last_transaksi_id'];
            $newTransaksiId = 'T' . sprintf('%03d', (int)substr($lastTransaksiId, 2) + 1);
            mysqli_begin_transaction($con);

            try {
                $query = "INSERT INTO Penarikan (IdPenarikan, IdNasabah, jumlah, tanggalPenarikan, jenisPenarikan, status) 
                          VALUES ('$newId', '$idNasabah', $jumlah, '$tanggal', 'PenarikanSaldo', 'Pending')";

                if (!mysqli_query($con, $query)) {
                    throw new Exception("Gagal menyimpan data penarikan");
                }
                $queryRiwayat = "INSERT INTO RiwayatTransaksi (IdTransaksi, IdNasabah, jenisTransaksi, jumlah, tanggalTransaksi) 
                                 VALUES ('$newTransaksiId', '$idNasabah', 'Penarikan', $jumlah, '$tanggal')";

                if (!mysqli_query($con, $queryRiwayat)) {
                    throw new Exception("Gagal menyimpan data riwayat transaksi");
                }

                mysqli_commit($con);
                $message = "Pengajuan penarikan saldo berhasil!";
            } catch (Exception $e) {
                mysqli_rollback($con);
                $message = "Gagal mengajukan penarikan: " . $e->getMessage();
            }
        }
    }

    if (isset($_POST['jenis_penarikan']) && $_POST['jenis_penarikan'] === 'poin') {
        $tanggal = $_POST['tanggalPoin'];
        $jumlahPoin = $_POST['jumlahPenukaran'];

        if (empty($tanggal) || empty($jumlahPoin)) {
            $message = "Harap isi semua field!";
        } elseif ($jumlahPoin <= 0) {
            $message = "Jumlah penukaran poin harus lebih dari 0!";
        } else {
            $idNasabah = $_SESSION['IdNasabah'];
            $queryPoin = "SELECT poin, saldo FROM Nasabah WHERE IdNasabah = '$idNasabah'";
            $resultPoin = mysqli_query($con, $queryPoin);

            if ($resultPoin) {
                $rowPoin = mysqli_fetch_assoc($resultPoin);

                if ($rowPoin['poin'] < $jumlahPoin) {
                    $message = "Poin tidak mencukupi untuk ditukarkan!";
                } else {
                    $tambahan_saldo = $jumlahPoin * 100;
                    $poin_baru = $rowPoin['poin'] - $jumlahPoin;
                    $saldo_baru = $rowPoin['saldo'] + $tambahan_saldo;
                    $queryLastId = "SELECT MAX(IdPenarikan) as last_id FROM Penarikan";
                    $resultLastId = mysqli_query($con, $queryLastId);
                    $rowLastId = mysqli_fetch_assoc($resultLastId);
                    $lastId = $rowLastId['last_id'];
                    $newId = 'PR' . sprintf('%03d', (int)substr($lastId, 2) + 1);
                    $queryLastTransaksiId = "SELECT MAX(IdTransaksi) as last_transaksi_id FROM RiwayatTransaksi";
                    $resultLastTransaksiId = mysqli_query($con, $queryLastTransaksiId);
                    $rowLastTransaksiId = mysqli_fetch_assoc($resultLastTransaksiId);
                    $lastTransaksiId = $rowLastTransaksiId['last_transaksi_id'];
                    $newTransaksiId = 'T' . sprintf('%03d', (int)substr($lastTransaksiId, 2) + 1);

                    mysqli_begin_transaction($con);
                    try {
                        $query = "INSERT INTO Penarikan (IdPenarikan, IdNasabah, jumlah, tanggalPenarikan, jenisPenarikan, status) 
                                 VALUES ('$newId', '$idNasabah', $jumlahPoin, '$tanggal', 'PenukaranPoin', 'Pending')";

                        if (!mysqli_query($con, $query)) {
                            throw new Exception("Gagal menyimpan data penukaran");
                        }

                        $queryRiwayat = "INSERT INTO RiwayatTransaksi (IdTransaksi, IdNasabah, jenisTransaksi, jumlah, tanggalTransaksi) 
                                         VALUES ('$newTransaksiId', '$idNasabah', 'Penarikan', $tambahan_saldo, '$tanggal')";

                        if (!mysqli_query($con, $queryRiwayat)) {
                            throw new Exception("Gagal menyimpan data riwayat transaksi");
                        }

                        $queryUpdate = "UPDATE Nasabah 
                                      SET poin = $poin_baru, saldo = $saldo_baru 
                                      WHERE IdNasabah = '$idNasabah'";

                        if (!mysqli_query($con, $queryUpdate)) {
                            throw new Exception("Gagal mengupdate saldo dan poin");
                        }

                        mysqli_commit($con);
                        $message = "Pengajuan penukaran poin berhasil! Poin berkurang $jumlahPoin, Saldo bertambah Rp " . number_format($tambahan_saldo, 0, ',', '.');
                    } catch (Exception $e) {
                        mysqli_rollback($con);
                        $message = "Gagal melakukan penukaran: " . $e->getMessage();
                    }
                }
            } else {
                $message = "Gagal mengecek poin: " . mysqli_error($con);
            }
        }
    }
}

$idNasabah = $_SESSION['IdNasabah'];
$queryRiwayat = "SELECT p.*, n.nama as nama_nasabah 
          FROM Penarikan p
          JOIN Nasabah n ON p.IdNasabah = n.IdNasabah
          WHERE p.IdNasabah = '$idNasabah'
          ORDER BY p.tanggalPenarikan DESC";
$resultRiwayat = mysqli_query($con, $queryRiwayat);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan Nasabah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.2/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .l-navbar {
            background-color: #F5FCEB;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .nav {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .nav_header {
            text-align: center;
            margin-bottom: 20px;
        }

        .nav_logo {
            text-decoration: none;
            color: #3A5F0B;
            font-weight: bold;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav_logo-icon {
            font-size: 30px;
            margin-right: 10px;
        }

        .nav_list {
            flex-grow: 1;
        }

        .nav_link {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: #3A5F0B;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .nav_link:hover,
        .nav_link.active {
            background-color: #D7F3E1;
            border-left: 4px solid #3A5F0B;
        }

        .nav_icon {
            font-size: 24px;
            margin-right: 10px;
        }

        /* Content styling */
        .content {
            margin-left: 250px;
            padding: 20px;
            background-color: #FBFDF6;
            min-height: 100vh;
        }

        h3 {
            color: #3A5F0B;
        }

        .card {
            border-radius: 10px;
        }

        .col-md-10 {
            margin-left: 100px;
        }

        .table {
            margin-left: 10px;
        }

        .dropdown-menu {
            background-color: #FBFDF6;
            border: 1px solid #D7F3E1;
            border-radius: 5px;
        }

        .dropdown-item {
            color: #3A5F0B;
            transition: background-color 0.3s;
        }

        .dropdown-item:hover {
            background-color: #D7F3E1;
            color: #3A5F0B;
        }

        .l-navbar {
            width: 250px;
            transition: all 0.3s;
        }

        .l-navbar.hidden {
            width: 0;
            overflow: hidden;
        }

        .content {
            margin-left: 250px;
            transition: margin-left 0.3s;
        }

        .l-navbar.hidden+.content {
            margin-left: 0;
        }

        .card-body {
            background-color: #567B65;
            color: white;
        }

        .card-header {
            background-color: #567B65;
            font-size: 30px;
        }

        .btn.w-100 {
            background-color: white;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="l-navbar" id="nav-bar">
            <nav class="nav">
                <div class="nav_header">
                    <a href="#" class="nav_logo">
                        <span class="nav_logo-name">GREEN SAVER</span>
                    </a>
                </div>

                <div class="nav_list">
                    <a href="dashboardNasabah.php" class="nav_link">
                        <i class='bx bx-home nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="setoranNasabah.php" class="nav_link">
                        <i class='bx bx-recycle nav_icon'></i>
                        <span class="nav_name">Setoran</span>
                    </a>
                    <a href="penarikan.php" class="nav_link active">
                        <i class='bx bx-dollar nav_icon'></i>
                        <span class="nav_name">Penarikan</span>
                    </a>
                </div>

                <a href="logout.php" class="nav_link">
                    <i class='bx bx-log-out nav_icon'></i>
                    <span class="nav_name">LogOut</span>
                </a>
            </nav>
        </div>

        <div class="content">
            <div class="d-flex justify-content-between align-items-center p-4">
                <button id="toggleSidebar" class="btn btn-outline-secondary">
                    <i class="bx bx-menu" style="font-size: 24px;"></i>
                </button>
                <h3>Penarikan</h3>
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2">Halo, <?php echo $_SESSION['nama']; ?></span>
                        <i class='bx bx-user-circle' style="font-size: 24px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="editNasabah.php">Edit Profil</a></li>
                    </ul>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info text-center" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="col-md-10 p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header text-white">
                                Penarikan Saldo
                            </div>
                            <div class="card-body">
                                <?php if ($saldo <= 0): ?>
                                    <div class="alert alert-warning text-center">
                                        Tidak dapat melakukan penarikan, saldo Anda kosong.
                                    </div>
                                <?php else: ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="jenis_penarikan" value="saldo">
                                        <div class="mb-3">
                                            <label for="tanggalSaldo" class="form-label">Tanggal</label>
                                            <input type="date" class="form-control" id="tanggalSaldo" name="tanggalSaldo" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="jumlahPenarikan" class="form-label">Jumlah Penarikan (Rp)</label>
                                            <input type="number" class="form-control" id="jumlahPenarikan" name="jumlahPenarikan"
                                                max="<?php echo $saldo; ?>" required>
                                        </div>
                                        <button type="submit" class="btn w-100">Ajukan Penarikan</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header text-white">
                                Penarikan Poin
                            </div>
                            <div class="card-body">
                                <?php if ($poin <= 0): ?>
                                    <div class="alert alert-warning text-center">
                                        Tidak dapat melakukan penukaran, poin Anda kosong.
                                    </div>
                                <?php else: ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="jenis_penarikan" value="poin">
                                        <div class="mb-3">
                                            <label for="tanggalPoin" class="form-label">Tanggal</label>
                                            <input type="date" class="form-control" id="tanggalPoin" name="tanggalPoin" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="jumlahPenukaran" class="form-label">Jumlah Penukaran Poin</label>
                                            <input type="number" class="form-control" id="jumlahPenukaran" name="jumlahPenukaran"
                                                max="<?php echo $poin; ?>" required>
                                        </div>
                                        <button type="submit" class="btn w-100">Ajukan Penukaran</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <table class="table table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis Penarikan</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($resultRiwayat) > 0) {
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($resultRiwayat)) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . $row['tanggalPenarikan'] . "</td>";
                                    echo "<td>" . ($row['jenisPenarikan'] === 'PenarikanSaldo' ? 'Penarikan Saldo' : 'Penukaran Poin') . "</td>";
                                    echo "<td>" . number_format($row['jumlah'], 0, ',', '.') . "</td>";
                                    echo "<td>" . $row['status'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>Tidak ada riwayat penarikan</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleSidebarButton = document.getElementById("toggleSidebar");
            const sidebar = document.getElementById("nav-bar");

            toggleSidebarButton.addEventListener("click", function() {
                sidebar.classList.toggle("hidden");
            });
        });
    </script>
</body>

</html>