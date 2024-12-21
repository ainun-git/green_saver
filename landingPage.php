<?php
session_start();
?>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        Green Saver
    </title>
    <script src="https://cdn.tailwindcss.com">
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow-x: hidden;
            max-width: 100%;
            max-block-size: 100%;
        }

        .ellipse {
            position: absolute;
            border-radius: 50%;
            opacity: 1;
        }

        .ellipse1 {
            width: 875.29px;
            height: 425px;
            background-color: #EBFFBF;
            transform: rotate(-2.35deg);
            top: -111.49px;
            left: 750px;
        }

        .ellipse2 {
            width: 1088.01px;
            height: 745.97px;
            background-color: rgba(181, 209, 147, 0.5);
            transform: rotate(-23.08deg);
            top: -250px;
            left: 894.44px;
        }

        .ellipse3 {
            width: 866px;
            height: 810px;
            background: linear-gradient(180deg, rgba(181, 209, 147, 0.03) 3%, rgba(93, 107, 75, 1) 100%);
            position: absolute;
            top: 1184px;
            left: -250px;
            border-radius: 60% 50% 100% 50% / 50% 60% 50% 40%;
            transform: rotate(-10deg);
        }

        .list-disc-custom {
            list-style-type: none;
        }

        .list-disc-custom li::before {
            content: "\f00c";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            color: #28a745;
            margin-right: 8px;
        }

        .image-container {
            position: relative;
            z-index: 1;
            bottom: 100px;
            left: 100px;
        }

        .login {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 16px;
            font-weight: bold;
            color: black;
        }

        .icon-container {
            position: relative;
            z-index: 1;
        }

        .daftar-sekarang-btn {
            background-color: #AFCA93;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 9999px;
            font-weight: 600;
            transition: background-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            width: 200px;
            height: 60px;
        }

        .daftar-sekarang-btn:hover {
            background-color: #2f855a;
        }

        .daftar-sekarang-btn:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(56, 161, 105, 0.3);
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-img {
            margin-right: 0.5rem;
            height: 50px;
            width: 55px;
        }

        .logo-text {
            color: #40423F;
            font-weight: bold;
        }

        .feature-section {
            margin-bottom: 8rem;
        }

        .section-title {
            font-size: 24px;
            font-weight: 800;
            color: black;
            text-align: center;
        }

        .section-description {
            font-size: 20px;
            color: black;
            text-align: center;
            margin-bottom: 1rem;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin-left: 180px;
        }

        @media (min-width: 768px) {
            .feature-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .feature-card {
            text-align: center;
            background-color: #EBFFBF;
            padding: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 230px;
            height: 230px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
        }

        .feature-icon {
            color: #5D6B4B;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .feature-title {
            color: black;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
        }

        .feature-description {
            color: black;
            font-weight: 300;
            font-size: 13px;
            text-align: center;
        }

        .solution-description {
            color: black;
            font-weight: normal;
            font-size: 20px;
            margin-bottom: 1rem;
        }

        .solution-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            margin-top: 30px;
        }

        @media (min-width: 768px) {
            .solution-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .solution-card {
            text-align: center;
        }

        .solution-icon {
            color: #2C7B2F;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .solution-title {
            color: black;
            font-weight: 600;
            font-size: 14px;
        }

        .solution-text {
            color: black;
            font-weight: normal;
            font-size: 14px;
        }

        section.mb-20.text-center {
            margin-bottom: 10rem;
        }

        .text-2xl {
            font-size: 2rem;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-black {
            color: #000;
        }

        .grid {
            display: grid;
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, 1fr);
        }

        .md\:grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .gap-8 {
            gap: 2rem;
        }

        .text-center {
            text-align: center;
        }

        .icon-container img {
            display: flex;
            width: 650px;
            margin-left: -70px;
            margin-top: -80px;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .text-left {
            text-align: left;
        }

        .list-inside {
            padding-left: 10rem;
        }

        li.mb-2 {
            margin-bottom: 2rem;
        }

        h2.text-2xl.font-bold.text-black.mb-4 {
            margin-bottom: 2rem;
            margin-left: 55rem;
            margin-right: 2rem;
        }

        .login a {
            color: #000;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login a:hover {
            color: #2C7B2F;
        }
    </style>
</head>

<body class="bg-[#F7FFE5]">
    <div class="ellipse ellipse1"></div>
    <div class="ellipse ellipse2"></div>
    <div class="ellipse ellipse3"></div>
    <div class="login">
        <a href="login.php">Login</a>
    </div>
    <main class="p-8">
        <div class="flex justify-between items-center mb-12">
            <div class="logo-container">
                <img alt="Green Saver Logo" class="logo-img" src="logo1.png" />
                <span class="logo-text">GREEN SAVER | </span>
                <span class="logo-text"> | BSU Cahaya Mandiri</span>
            </div>
        </div>
        <div class="flex flex-col md:flex-row items-center md:items-start mb-12">
            <div class="md:w-1/2 text-center md:text-left mb-8 md:mb-0">
                <h1 class="text-black font-extrabold mb-2 leading-tight text-center md:text-left" style="font-size: 48px;">
                    Jadilah Bagian dari Komunitas Hijau Kami
                </h1>
                <p class="text-black mb-6 text-center md:text-left">
                    Gabung sekarang dan manfaatkan kesempatan untuk lingkungan yang lebih bersih dan sehat.
                </p>
                <div class="flex justify-center md:justify-start">
                    <button class="daftar-sekarang-btn">
                        <a href="signUp.php"> Daftar Sekarang</a>
                    </button>
                </div>
            </div>
            <div class="md:w-1/2 image-container">
                <img alt="Illustration of a person recycling" class="mx-auto" height="270" src="img1.png" width="420" />
            </div>
        </div>
        <section class="mb-20 text-center">
            <h2 class="text-2xl font-bold text-black mb-2">
                Penawaran Kami
            </h2>
            <p class="solution-description">
                Solusi Pengelolaan Sampah yang Mudah dan Menguntungkan
            </p>
            <div class="solution-grid">
                <div class="solution-card">
                    <i class="fas fa-recycle solution-icon"></i>
                    <h3 class="solution-title">
                        Pengelolaan Sampah yang Praktis
                    </h3>
                    <p class="solution-text">
                        Bawa sampah Anda langsung ke bank sampah kami dan nikmati proses yang cepat dan mudah.
                    </p>
                </div>
                <div class="solution-card">
                    <i class="fas fa-money-bill-wave solution-icon"></i>
                    <h3 class="solution-title">
                        Ubah Sampah Jadi Uang
                    </h3>
                    <p class="solution-text">
                        Tukarkan sampah Anda! Setiap transaksi langsung masuk ke saldo Anda dan bisa dicairkan kapan saja.
                    </p>
                </div>
                <div class="solution-card">
                    <i class="fas fa-leaf solution-icon"></i>
                    <h3 class="solution-title">
                        Dukung Lingkungan Lebih Bersih
                    </h3>
                    <p class="solution-text">
                        Bergabunglah dan kelola sampah untuk menjaga kebersihan bumi bagi generasi mendatang.
                    </p>
                </div>
            </div>
            </div>
        </section>
        <section class="feature-section">
            <h2 class="section-title">
                Fitur Unggulan yang Memudahkan Anda
            </h2>
            <p class="section-description">
                Menyediakan kemudahan dan keuntungan dalam pengelolaan sampah Anda.
            </p>
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fas fa-calculator feature-icon"></i>
                    <h3 class="feature-title">
                        Perhitungan Otomatis
                    </h3>
                    <p class="feature-description">
                        Setiap kilogram sampah yang Anda serahkan dihitung secara otomatis, sehingga Anda selalu tahu nilai yang Anda dapatkan.
                    </p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-wallet feature-icon"></i>
                    <h3 class="feature-title">
                        Saldo Digital
                    </h3>
                    <p class="feature-description">
                        Hasil penukaran sampah langsung masuk ke saldo digital Anda. Pantau saldo secara real-time dan tarik kapan saja.
                    </p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-receipt feature-icon"></i>
                    <h3 class="feature-title">
                        Riwayat Transaksi Lengkap
                    </h3>
                    <p class="feature-description">
                        Cek riwayat transaksi Anda dengan mudah, termasuk jenis sampah yang diserahkan dan jumlah penghasilan.
                    </p>
                </div>
            </div>
        </section>
        <h2 class="text-2xl font-bold text-black mb-4">
            Manfaat Bank Sampah
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="text-center icon-container">
                <img alt="Icon of a recycling bin" class="mx-auto mb-4" src="img2.png" />
            </div>
            <div class="text-left">
                <ul class="list-disc-custom list-inside text-black">
                    <li class="mb-2">Mengurangi Sampah</li>
                    <li class="mb-2">Pengelolaan Sampah yang Efektif</li>
                    <li class="mb-2">Peningkatan Kualitas Udara</li>
                    <li class="mb-2">Mendukung Program Lingkungan</li>
                    <li class="mb-2">Memberikan Manfaat Lingkungan</li>
                </ul>
            </div>
        </div>
        </section>
    </main>
</body>

</html>