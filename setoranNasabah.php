<?php
session_start();

if (!isset($_SESSION['IdNasabah'])) {
    header("Location: setoranNasabah.php");
    exit();
}

$user_id = $_SESSION['IdNasabah'];
$username = $_SESSION['username'];

include('koneksi.php');
$query = "
    SELECT setoransampah.tanggalSetor AS Tanggal, 
           kategorisampah.kategori AS jenisSampah, 
           setoransampah.totalHarga, 
           nasabah.poin 
    FROM setoransampah
    INNER JOIN nasabah ON setoransampah.IdNasabah = nasabah.IdNasabah
    INNER JOIN kategorisampah ON setoransampah.kodeKategori = kategorisampah.kodeKategori
    WHERE setoransampah.IdNasabah = '$user_id' 
    ORDER BY setoransampah.tanggalSetor DESC";
$resultSetoran = mysqli_query($con, $query);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setoran Nasabah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.2/css/boxicons.min.css" rel="stylesheet">
</head>
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

    .content {
        margin-left: 250px;
        padding: 20px;
        background-color: #FBFDF6;
        min-height: 100vh;
    }

    h3 {
        color: #3A5F0B;
    }

    .table-bordered th,
    .table-bordered td {
        text-align: center;
        vertical-align: middle;
    }

    .table-bordered th {
        background-color: #F0F5E3;
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
</style>

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
                    <a href="setoranNasabah.php" class="nav_link active">
                        <i class='bx bx-recycle nav_icon'></i>
                        <span class="nav_name">Setoran</span>
                    </a>
                    <a href="Penarikan.php" class="nav_link">
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
                <h3>Setoran Nasabah</h3>
            </div>

            <div class="table-responsive px-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis Sampah</th>
                            <th>Total Poin</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($resultSetoran) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($resultSetoran)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($row['Tanggal'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['jenisSampah']); ?></td>
                                    <td><?php echo htmlspecialchars($row['poin']); ?></td>
                                    <td><?php echo htmlspecialchars($row['totalHarga']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Data Kosong</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>