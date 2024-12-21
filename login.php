<?php
session_start();
include "koneksi.php";

session_regenerate_id(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = mysqli_real_escape_string($con, trim($_POST['username']));
  $password = $_POST['password'];

  if (empty($username) || empty($password)) {
    $error = "Username dan password harus diisi!";
  } else {
    $query = "SELECT * FROM Pengurus WHERE username = '$username'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
      $user = mysqli_fetch_assoc($result);

      if ($password === $user['password']) {
        $_SESSION = [];
        $_SESSION['IdPengurus'] = $user['IdPengurus'];
        $_SESSION['namaPengurus'] = $user['namaPengurus'];
        $_SESSION['jabatan'] = $user['jabatan'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboardPengurus.php");
        exit();
      } else {
        $error = "Password salah!";
      }
    } else {
      $query = "SELECT * FROM Nasabah WHERE username = '$username'";
      $result = mysqli_query($con, $query);

      if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
          $_SESSION = [];
          $_SESSION['IdNasabah'] = $user['IdNasabah'];
          $_SESSION['nama'] = $user['nama'];
          $_SESSION['username'] = $user['username'];
          header("Location: dashboardNasabah.php");
          exit();
        } else {
          $error = "Password salah!";
        }
      } else {
        $error = "Username tidak ditemukan!";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Green Saver</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #FBFDF6;
      margin: 0;
      padding: 0;
    }

    .container-fluid {
      background-color: #F7FFE5;
    }

    .bg-light-green {
      background-color: #FBFDF6;
    }

    .welcome-text {
      font-size: 2.5rem;
      font-weight: bold;
      color: #51803A;
    }

    .image-placeholder {
      width: 250px;
      height: 250px;
      border-radius: 50%;
      margin-top: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-image: url('logo.png');
      background-size: 130%;
      background-position: center;
      background-repeat: no-repeat;
    }

    .login-form {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .login-form h2 {
      font-weight: bold;
      color: #333;
    }

    .login-form p {
      font-size: 14px;
      color: #666;
    }

    .input-group-text {
      border: none;
      background-color: #cde4d4;
      color: #4b8c5a;
    }

    .forgot-password {
      font-size: 14px;
      color: #4b8c5a;
      text-decoration: none;
    }

    .forgot-password:hover {
      text-decoration: underline;
    }

    .signup-link {
      color: #4b8c5a;
      font-weight: bold;
      text-decoration: none;
    }

    .signup-link:hover {
      text-decoration: underline;
    }

    .btn.w-100 {
      background-color: #51803A;
      color: white;
    }
  </style>
</head>

<body>
  <div class="container-fluid vh-100">
    <div class="row h-100">
      <div class="col-md-6 d-flex flex-column justify-content-center align-items-center bg-light-green">
        <h1 class="welcome-text text-uppercase">Welcome Back</h1>
        <div class="image-placeholder"></div>
      </div>
      <div class="col-md-6 d-flex flex-column justify-content-center px-5">
        <div class="login-form">
          <h2 class="text-center">Login</h2>
          <p class="text-center">Welcome back! Please login to your account.</p>

          <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center">
              <?php echo $error; ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="form-group mb-3">
              <div class="input-group">
                <span class="input-group-text bg-light-green">
                  <i class="bx bx-user"></i>
                </span>
                <input type="text" class="form-control" name="username" placeholder="User Name" required>
              </div>
            </div>
            <div class="form-group mb-3">
              <div class="input-group">
                <span class="input-group-text bg-light-green">
                  <i class="bx bx-lock"></i>
                </span>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Remember Me</label>
              </div>
              <a href="#" class="forgot-password">Forgot Password?</a>
            </div>
            <button type="submit" class="btn btn-success w-100">LOGIN</button>
          </form>
          <div class="text-center mt-3">
            <p>New User? <a href="signUp.php" class="signup-link">SignUp</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>