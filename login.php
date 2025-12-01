<?php
require_once 'config.php';

// Jika sudah login, redirect ke dashboard
if (is_logged_in()) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

// Proses login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $password = clean_input($_POST['password']);
    
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Set session
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['foto_profile'] = $user['foto_profile'];
        
        // Log aktivitas
        log_aktivitas($user['id_user'], 'Login', 'User berhasil login');
        
        header("Location: dashboard.php");
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Digital</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8B7355 0%, #D2B48C 50%, #C19A6B 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: #FFF8DC;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #8B7355, #A0826D);
            color: #FFF8DC;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 60px;
            margin-bottom: 15px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .login-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .login-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            color: #6B5744;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #A0826D;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #D2B48C;
            border-radius: 10px;
            font-size: 15px;
            background: white;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #8B7355;
            box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
        }
        
        .error-message {
            background: #FFE4E1;
            color: #8B4513;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #CD5C5C;
            font-size: 14px;
        }
        
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #8B7355, #A0826D);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 115, 85, 0.4);
        }
        
        .login-btn:active {
            transform: translateY(0);
        }
        
        .info-box {
            background: #F5DEB3;
            padding: 20px;
            border-radius: 10px;
            margin-top: 25px;
            border: 2px dashed #D2B48C;
        }
        
        .info-box h4 {
            color: #6B5744;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .info-box p {
            color: #8B7355;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .info-box strong {
            color: #6B5744;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-book-reader"></i>
            <h1>Perpustakaan Digital</h1>
            <p>Sistem Manajemen Perpustakaan</p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" required autocomplete="off">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>
            
            <div class="info-box">
                <h4><i class="fas fa-info-circle"></i> Akun Demo</h4>
                <p>
                    <strong>Admin:</strong> admin / admin123<br>
                    <strong>Petugas:</strong> petugas1 / petugas123
                </p>
            </div>
        </div>
    </div>
</body>
</html>