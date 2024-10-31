<?php
session_start();

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'], $_POST['password'], $_POST['email'])) {
        require_once 'config.php'; // Memanggil file config.php yang berisi fungsi opendb()

        // Memanggil fungsi opendb() untuk mendapatkan koneksi ke database
        $conn = opendb();

        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password
        $email = $_POST['email'];

        $sql = "INSERT INTO users (username, password, email) VALUES ($1, $2, $3)";
        $result = pg_query_params($conn, $sql, array($username, $password, $email));

        if ($result) {
            $successMessage = 'Registration successful! You can now <a href="index.php">login</a>.';
        } else {
            $errorMessage = 'Registration failed: ' . pg_last_error($conn);
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
    <title>Register</title>
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

        #registerForm {
            width: 400px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #registerForm h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        #registerForm input[type="text"],
        #registerForm input[type="password"],
        #registerForm input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }

        #registerForm input[type="submit"] {
            background-color: #596FB7;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        #registerForm input[type="submit"]:hover {
            background-color: #11235A;
        }

        .errorMessage, .successMessage {
            text-align: center;
            margin-bottom: 10px;
        }

        .errorMessage {
            color: #990000;
        }

        .successMessage {
            color: #009900;
        }
    </style>
</head>
<body>
    <div id="registerForm">
        <h2>Register</h2>

        <?php if ($errorMessage != '') : ?>
            <p class="errorMessage"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <?php if ($successMessage != '') : ?>
            <p class="successMessage"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <form action="" method="POST" name="frmRegister" id="frmRegister">
            <label for="username">Username</label>
            <input name="username" type="text" id="username" required>
            <label for="password">Password</label>
            <input name="password" type="password" id="password" required>
            <label for="email">Email</label>
            <input name="email" type="text" id="email" required>
            <input name="btnRegister" type="submit" value="Register">
        </form>

        <p>Already have an account? <a href="index.php">Login</a></p>
    </div>
</body>
</html>
