<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['IdNasabah'])) {
    header("Location: dashboardNasabah.php");
    exit();
}

$username = $_SESSION['username'];
$idNasabah = $_SESSION['IdNasabah'];

$sqlSetoran = "SELECT SUM(berat) AS total_setoran FROM setoransampah WHERE IdNasabah = '$idNasabah'";
$resultSetoran = mysqli_query($con, $sqlSetoran);
$dataSetoran = mysqli_fetch_assoc($resultSetoran);
$totalSetoran = $dataSetoran['total_setoran'] ? $dataSetoran['total_setoran'] : 0;
$sqlTabungan = "SELECT 
    (COALESCE((SELECT SUM(totalHarga) 
               FROM setoransampah 
               WHERE IdNasabah = '$idNasabah'), 0) -
     COALESCE((SELECT SUM(jumlah) 
               FROM penarikan 
               WHERE IdNasabah = '$idNasabah' 
               AND jenisPenarikan = 'PenarikanSaldo' 
               AND status = 'Diterima'), 0)) AS total_tabungan";

$resultTabungan = mysqli_query($con, $sqlTabungan);
$dataTabungan = mysqli_fetch_assoc($resultTabungan);
$totalTabungan = $dataTabungan['total_tabungan'] ? number_format($dataTabungan['total_tabungan'], 0, ',', '.') : '0';


$sqlKategori = "SELECT * FROM kategorisampah";
$resultKategori = mysqli_query($con, $sqlKategori);

date_default_timezone_set("Asia/Jakarta");

$year = isset($_GET['year']) ? $_GET['year'] : date("Y");
$month = isset($_GET['month']) ? $_GET['month'] : date("m");

$months = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
];

$sqlRiwayatTransaksi = "SELECT * FROM riwayattransaksi 
                               WHERE IdNasabah = '$idNasabah' 
                               ORDER BY tanggalTransaksi DESC 
                               LIMIT 3";
$resultRiwayatTransaksi = mysqli_query($con, $sqlRiwayatTransaksi);

function getCalendar($year, $month)
{
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $firstDayOfMonth = date("w", strtotime("$year-$month-01"));

    $calendar = [];
    $day = 1;

    for ($row = 0; $row < 6; $row++) {
        $week = [];
        for ($col = 0; $col < 7; $col++) {
            if ($row == 0 && $col < $firstDayOfMonth) {
                $week[] = '';
            } elseif ($day <= $daysInMonth) {
                $week[] = $day;
                $day++;
            } else {
                $week[] = '';
            }
        }
        $calendar[] = $week;
    }
    return $calendar;
}
$calendar = getCalendar($year, $month);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Nasabah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    .card-kalender {
        margin-top: 3px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        overflow: hidden;
        width: 35%;
        margin-left: auto;
        margin-right: -32px;
    }

    .card-header-kalender {
        background-color: #567B65;
        color: #fff;
        padding: 10px 20px;
        text-align: left;
        font-size: 18px;
    }

    .card-header-kalender h5 {
        margin: 0;
        font-weight: 600;
    }

    .card-header-kalender .d-flex {
        align-items: center;
        justify-content: space-between;
    }

    .card-header-kalender .btn-secondary {
        background-color: rgb(171, 222, 184);
        border: none;
        padding: 5px 10px;
        color: white;
    }

    .card-header-kalender .btn-secondary:hover {
        background-color: rgb(171, 222, 184);
    }

    .card-body-kalender {
        padding: 20px;
    }

    .card-body-kalender .btn-secondary {
        background-color: rgb(171, 222, 184);
        border: none;
        padding: 5px 15px;
        color: white;
    }

    .card-body-kalender .btn-secondary:hover {
        background-color: rgb(171, 222, 184);
    }

    .card-body-kalender h6 {
        font-size: 18px;
        font-weight: 500;
        margin: 0;
    }

    table {
        width: 100%;
        table-layout: fixed;
        text-align: center;
    }

    th {
        background-color: #f8f9fa;
        color: #495057;
        padding: 8px;
        font-weight: 600;
    }

    td {
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ddd;
    }

    td:hover {
        background-color: #f0f0f0;
        cursor: pointer;
    }

    td.current-day {
        background-color: #007bff;
        color: white;
        border-radius: 50%;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .card-kalender {
            margin-top: 10px;
        }

        .card-header-kalender h5 {
            font-size: 16px;
        }

        .card-body-kalender h6 {
            font-size: 16px;
        }

        table {
            font-size: 12px;
        }

        th,
        td {
            padding: 8px;
        }

        .btn-secondary {
            padding: 4px 10px;
        }
    }

    .col-md-8 {
        margin-top: -30%;
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
                    <a href="dashboardNasabah.php" class="nav_link active">
                        <i class='bx bx-home nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="setoranNasabah.php" class="nav_link">
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
                <button id="toggleSidebar" class="btn btn-outline-secondary">
                    <i class="bx bx-menu" style="font-size: 24px;"></i>
                </button>
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="me-2">Halo, <?php echo htmlspecialchars($username); ?></span>
                        <i class='bx bx-user-circle' style="font-size: 24px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="editNasabah.php">Edit Profil</a></li>
                    </ul>
                </div>
            </div>

            <div class="row px-4">
                <div class="col-md-12 mb-3">
                    <div class="card-kalender">
                        <div class="card-header-kalender">
                            <div class="d-flex justify-content-between">
                                <h5>Kalender Tahun <?php echo $year; ?></h5>
                                <div>
                                    <a href="?year=<?php echo $year - 1; ?>&month=<?php echo $month; ?>" class="btn btn-sm btn-secondary">&laquo;</a>
                                    <a href="?year=<?php echo $year + 1; ?>&month=<?php echo $month; ?>" class="btn btn-sm btn-secondary">&raquo;</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body-kalender">
                            <div class="d-flex justify-content-between">
                                <button onclick="changeMonth(-1)" class="btn btn-sm btn-secondary">&laquo;</button>
                                <h6 class="text-center"><?php echo $months[$month - 1] . " " . $year; ?></h6>
                                <button onclick="changeMonth(1)" class="btn btn-sm btn-secondary">&raquo;</button>
                            </div>
                            <table class="table table-bordered table-sm mt-3">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sun</th>
                                        <th class="text-center">Mon</th>
                                        <th class="text-center">Tue</th>
                                        <th class="text-center">Wed</th>
                                        <th class="text-center">Thu</th>
                                        <th class="text-center">Fri</th>
                                        <th class="text-center">Sat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($calendar as $week): ?>
                                        <tr>
                                            <?php foreach ($week as $day): ?>
                                                <td class="text-center"><?php echo $day ? $day : ''; ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <span>Total Setoran</span>
                            <h5><?php echo $totalSetoran; ?> kg</h5>
                            <i class='bx bx-recycle' style="font-size: 24px;"></i>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <span>Total Tabungan</span>
                            <h5>Rp. <?php echo $totalTabungan; ?>,-</h5>
                            <i class='bx bx-briefcase' style="font-size: 24px;"></i>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5>Kategori Sampah</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Jenis Sampah</th>
                                        <th>Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($resultKategori)) { ?>
                                        <tr>
                                            <td><?php echo $row['kategori']; ?></td>
                                            <td><?php echo $row['namaKategori']; ?></td>
                                            <td>Rp. <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Transaksi Terakhir</h5>
                            <a href="#" class="small">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Jenis Transaksi</th>
                                        <th>Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($resultRiwayatTransaksi)) {
                                        echo "<tr>";
                                        echo "<td>" . $no++ . "</td>";
                                        echo "<td>" . $row['tanggalTransaksi'] . "</td>";
                                        echo "<td>" . $row['jenisTransaksi'] . "</td>";
                                        echo "<td>Rp. " . number_format($row['jumlah'], 0, ',', '.') . "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function changeMonth(direction) {
            let currentMonth = <?php echo $month; ?>;
            let currentYear = <?php echo $year; ?>;
            currentMonth += direction;

            if (currentMonth > 12) {
                currentMonth = 1;
                currentYear++;
            } else if (currentMonth < 1) {
                currentMonth = 12;
                currentYear--;
            }
            window.location.href = `?year=${currentYear}&month=${currentMonth}`;
        }
    </script>
</body>
</html>