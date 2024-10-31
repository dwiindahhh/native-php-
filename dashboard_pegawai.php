<?php
session_start();

require 'config.php';

// Buka koneksi ke database PostgreSQL
$conn = opendb();

// Ambil data produk dari database
$query = "SELECT * FROM products";
$result = pg_query($conn, $query);

if (!$result) {
    die("Error in SQL query: " . pg_last_error());
}

// Mengambil hasil sebagai array asosiatif
$results = pg_fetch_all($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Belanja</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-image: url('https://images.pexels.com/photos/9386052/pexels-photo-9386052.jpeg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
        }
        .bg-opacity-90 {
            background-color: rgba(255, 255, 255, 0.9);
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mx-auto mt-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Daftar Buah</h2>
            <!-- Tambah link login/logout sesuai kebutuhan -->
            <?php if (isset($_SESSION['user_is_logged_in']) && $_SESSION['user_is_logged_in'] === true) : ?>
                <a href="logout.php" class="text-red-500 hover:text-red-700">Logout</a>
            <?php else: ?>
                <a href="login.php" class="text-green-500 hover:text-green-700">Login</a>
            <?php endif; ?>
        </div>
        <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php
                // Loop through products if available
                if ($results) : ?>
                    <?php foreach ($results as $row): ?>
                        <div class="border border-gray-300 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <h3 class="text-xl font-bold text-gray-700"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="text-gray-600">ID: <?php echo htmlspecialchars($row['id']); ?></p>
                            <p class="text-gray-600">Harga: <?php echo htmlspecialchars($row['price']); ?></p>
                            <p class="text-gray-600">Stok: <span id="stock-<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['stock']); ?></span></p>
                            <div class="mt-4">
                                <button class="text-blue-500 hover:text-blue-700" onclick="editBuah(<?php echo $row['id']; ?>)">Edit Buah</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="border border-gray-300 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                    <h3 class="text-xl font-bold text-gray-700">Tambah Buah</h3>
                    <div class="mt-4">
                        <a href="tambah_buah.php" class="text-blue-500 hover:text-blue-700">Tambah Buah Baru</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function editBuah(productId) {
            window.location.href = 'edit.php?id=' + productId;
        }
    </script>
</body>
</html>

<?php
// Tutup koneksi ke database
pg_close($conn);
?>
