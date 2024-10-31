<?php
session_start();

require 'config.php';

// Buka koneksi ke database PostgreSQL
$connection = opendb();

// Periksa koneksi
if (!$connection) {
    die("Koneksi database gagal: " . pg_last_error());
}

// Periksa apakah user sudah login
if (!isset($_SESSION['user_is_logged_in']) || $_SESSION['user_is_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Ambil data produk dari database
$query = "SELECT * FROM products";
$result = pg_query($connection, $query);
$results = pg_fetch_all($result);

$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

function closedb($connection) {
    pg_close($connection);
}
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
            <a href="cart.php" class="relative">
                <i class="fas fa-shopping-cart text-2xl text-gray-700"></i>
                <span id="cart-count" class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo $cartCount; ?></span>
            </a>
        </div>
        <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php
                // Loop through products if available
                if ($results && count($results) > 0) : ?>
                    <?php foreach ($results as $row): ?>
                        <div class="border border-gray-300 p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                            <h3 class="text-xl font-bold text-gray-700"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="text-gray-600">ID: <?php echo htmlspecialchars($row['id']); ?></p>
                            <p class="text-gray-600">Harga: <?php echo htmlspecialchars($row['price']); ?></p>
                            <p class="text-gray-600">Stok: <span id="stock-<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['stock']); ?></span></p>
                            <div class="mt-4">
                                <button class="text-green-500 hover:text-green-700" onclick="addToCart(<?php echo $row['id']; ?>)">Masukkan Keranjang</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        function addToCart(productId) {
            $.ajax({
                url: 'add_to_cart.php',
                type: 'POST',
                data: { id: productId },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        $('#cart-count').text(data.cart_count);
                        $('#stock-' + productId).text(data.new_stock);
                    } else {
                        alert(data.error);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menambahkan ke keranjang.');
                }
            });
        }
    </script>
</body>
</html>

<?php
// Tutup koneksi ke database
closedb($connection);
?>
