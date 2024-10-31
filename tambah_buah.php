<?php
session_start();

require 'config.php';

// Periksa apakah user sudah login
if (!isset($_SESSION['user_is_logged_in']) || $_SESSION['user_is_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Proses tambah buah baru
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Simpan ke database
    $conn = opendb();
    $query = "INSERT INTO products (name, price, stock) VALUES ($1, $2, $3)";
    $result = pg_query_params($conn, $query, array($name, $price, $stock));

    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }

    pg_close($conn);

    header('Location: dashboard_pegawai.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buah</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Tambah Buah Baru</h2>
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <form action="tambah_buah.php" method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-bold mb-2">Nama Buah:</label>
                    <input type="text" name="name" id="name" class="block w-full text-gray-700 py-2 px-3 border border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700 font-bold mb-2">Harga:</label>
                    <input type="text" name="price" id="price" class="block w-full text-gray-700 py-2 px-3 border border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="stock" class="block text-gray-700 font-bold mb-2">Stok:</label>
                    <input type="text" name="stock" id="stock" class="block w-full text-gray-700 py-2 px-3 border border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Tambah Buah</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
