<?php
session_start();

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'], $_POST['password'])) {
        require_once 'config.php'; // Memanggil file config.php yang berisi fungsi opendb()

        // Memanggil fungsi opendb() untuk mendapatkan koneksi ke database
        $conn = opendb();

        $username = $_POST['username'];
        $password = $_POST['password'];

        // Query untuk mendapatkan user berdasarkan username
        $sql = "SELECT * FROM users WHERE username = $1";
        $result = pg_query_params($conn, $sql, array($username));

        if ($result) {
            if (pg_num_rows($result) > 0) {
                $user = pg_fetch_assoc($result);

                // Verifikasi password
                if (password_verify($password, $user['password'])) {
                    // Set session user
                    $_SESSION['user'] = $user;
                    $_SESSION['user_is_logged_in'] = true; // Tambahkan session ini setelah login berhasil
                    header("Location: dashboard_user.php");
                    exit;
                } else {
                    $errorMessage = 'Invalid username or password';
                }
            } else {
                $errorMessage = 'Username not found';
            }
        } else {
            $errorMessage = 'Query failed: ' . pg_last_error($conn);
        }

        // Tutup koneksi ke database
        pg_close($conn);
    } else {
        $errorMessage = 'All fields are required';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('https://images.pexels.com/photos/9386052/pexels-photo-9386052.jpeg');
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        #loginForm {
            width: 400px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #loginForm h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        #loginForm input[type="text"],
        #loginForm input[type="password"],
        #loginForm input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }

        #loginForm input[type="submit"] {
            background-color: #596FB7;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        #loginForm input[type="submit"]:hover {
            background-color: #11235A;
        }

        .errorMessage {
            text-align: center;
            color: #990000;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div id="loginForm">
        <h2>Login</h2>

        <?php if ($errorMessage != '') : ?>
            <p class="errorMessage"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="username">Username</label>
            <input name="username" type="text" id="username" required>
            <label for="password">Password</label>
            <input name="password" type="password" id="password" required>
            <input name="login" type="submit" value="Login">
        </form>

        <p>Don't have an account? <a href="daftar.php">Register</a></p>
    </div>
</body>
</html>
