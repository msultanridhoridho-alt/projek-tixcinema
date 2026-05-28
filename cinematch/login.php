<?php
include_once 'database.php';

$error = "";
$success = "";

// 1. PROSES AUTHENTIKASI POST (Untuk Form Manual)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- AKSI SIGN IN MANUAL ---
    if (isset($_POST['action_login'])) {
        $usernameOrEmail = strtolower(trim($_POST['username_email']));
        $password = $_POST['password'];
        $userFound = false;

        foreach ($_SESSION['users_db'] as $usernameKey => $data) {
            if (($usernameKey === $usernameOrEmail || $data['email'] === $usernameOrEmail) && $data['password'] === $password) {
                $_SESSION['user'] = $usernameKey;
                $_SESSION['role'] = $data['role'];
                $userFound = true;
                
                if ($_SESSION['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            }
        }
        if (!$userFound) { $error = "Kombinasi User/Email dan Password salah!"; }
    }

    // --- AKSI REGISTER MANUAL ---
    if (isset($_POST['action_register'])) {
        $regUser = strtolower(trim($_POST['reg_username']));
        $regEmail = strtolower(trim($_POST['reg_email']));
        $regPass = $_POST['reg_password'];

        if (isset($_SESSION['users_db'][$regUser])) {
            $error = "Username tersebut sudah terdaftar di sistem!";
        } else {
            $_SESSION['users_db'][$regUser] = [
                "password" => $regPass,
                "role" => "user",
                "email" => $regEmail
            ];
            $success = "Akun berhasil dibuat! Silakan masuk menggunakan form Login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Autentikasi - CineMatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <style>
        body { background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1200') no-repeat center center fixed; background-size: cover; color: white; }
        .auth-card { background-color: rgba(0, 0, 0, 0.9); border: 1px solid #333; border-radius: 8px; width: 430px; padding: 40px; box-shadow: 0px 10px 25px rgba(0,0,0,0.7); }
        .form-control-custom { background-color: #333 !important; color: white !important; border: 1px solid #444 !important; height: 48px; }
        .form-control-custom:focus { border-color: #E50914 !important; box-shadow: none !important; }
        .form-control-custom::placeholder { color: #aaaaaa; }
        
        .google-btn-container { width: 100%; display: flex; justify-content: center; min-height: 45px; }
        
        .toggle-link { color: #E50914; font-weight: bold; text-decoration: none; cursor: pointer; }
        .toggle-link:hover { text-decoration: underline; color: #ff1e27; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div id="g_id_onload"
        data-client_id="1031057531773-v761376isgch89b88ptc9gmd0g29988q.apps.googleusercontent.com"
        data-context="signin"
        data-ux_mode="popup"
        data-callback="handleCredentialResponse"
        data-auto_prompt="false">
    </div>

    <div class="auth-card">
        <h2 class="text-center text-danger fw-bolder mb-4" style="letter-spacing: 1.5px; font-size: 2rem;">TIXCINEMA</h2>

        <?php if($error): ?> <div class="alert alert-danger p-2 small text-center fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i> <?= $error; ?></div> <?php endif; ?>
        <?php if($success): ?> <div class="alert alert-success p-2 small text-center fw-bold"><i class="fa-solid fa-circle-check me-1"></i> <?= $success; ?></div> <?php endif; ?>

        <div id="login-box">
            <h4 class="mb-3 fw-bold text-white">Sign In</h4>
            <form method="POST" action="">
                <input type="hidden" name="action_login" value="1">
                <div class="mb-3">
                    <input type="text" name="username_email" class="form-control form-control-custom" placeholder="Username atau Email" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control form-control-custom" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-danger w-100 fw-bold py-2.5 mb-2 fs-6" style="background-color: #E50914; border: none;">Sign In</button>
            </form>

            <div class="d-flex align-items-center my-4">
                <hr class="flex-grow-1 border-secondary m-0">
                <span class="px-3 text-white fw-bold small" style="font-size: 0.75rem; letter-spacing: 1px;">ATAU MASUK MENGGUNAKAN</span>
                <hr class="flex-grow-1 border-secondary m-0">
            </div>

            <div class="google-btn-container">
                <div class="g_id_signin" 
                    data-type="standard" 
                    data-shape="rectangular" 
                    data-theme="white" 
                    data-text="continue_with" 
                    data-size="large" 
                    data-logo_alignment="left"
                    data-width="350">
                </div>
            </div>

            <p class="small mt-4 mb-0 text-white" style="font-size: 0.9rem;">
                Baru di TixCinema? <span class="toggle-link" onclick="switchForm('register')">Daftar sekarang.</span>
            </p>
        </div>

        <div id="register-box" class="d-none">
            <h4 class="mb-3 fw-bold text-white">Sign Up (Register)</h4>
            <form method="POST" action="">
                <input type="hidden" name="action_register" value="1">
                <div class="mb-3">
                    <input type="text" name="reg_username" class="form-control form-control-custom" placeholder="Buat Username Baru" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="reg_email" class="form-control form-control-custom" placeholder="Alamat Email Aktif" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="reg_password" class="form-control form-control-custom" placeholder="Buat Password Kuat" required>
                </div>
                <button type="submit" class="btn btn-success w-100 fw-bold py-2.5 mb-3 fs-6">Daftar Akun Baru</button>
            </form>

            <p class="small mt-4 mb-0 text-white" style="font-size: 0.9rem;">
                Sudah punya akun? <span class="text-primary fw-bold text-decoration-none" style="cursor: pointer;" onclick="switchForm('login')">Masuk di sini.</span>
            </p>
        </div>

    </div>

    <script>
        function switchForm(target) {
            const loginBox = document.getElementById('login-box');
            const regBox = document.getElementById('register-box');
            if (target === 'register') {
                loginBox.classList.add('d-none');
                regBox.classList.remove('d-none');
            } else {
                regBox.classList.add('d-none');
                loginBox.classList.remove('d-none');
            }
        }

        // Ketika pengguna sukses memilih salah satu akun Gmail-nya
        function handleCredentialResponse(response) {
            const responsePayload = parseJwt(response.credential);
            
            alert("Login Berhasil Via Google API!\n\nSelamat Datang, " + responsePayload.name + " (" + responsePayload.email + ")");
            window.location.href = "index.php";
        }

        function parseJwt (token) {
            var base64Url = token.split('.')[1];
            var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            var jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
            return JSON.parse(jsonPayload);
        };
    </script>
</body>
</html>