<?php
session_start();

require 'config.php';

// Buka koneksi ke database PostgreSQL
$connection = opendb();

// Inisialisasi variabel untuk menyimpan produk di keranjang
$cartItems = [];

// Periksa apakah keranjang tidak kosong
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_map(function($id) { return "'$id'"; }, $productIds));

    // Ambil informasi produk dari database berdasarkan ID produk yang ada di keranjang
    $query = "SELECT * FROM products WHERE id IN ($placeholders)";
    $result = pg_query($connection, $query);

    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }

    // Ambil hasil query dan tambahkan jumlah produk dari keranjang
    while ($row = pg_fetch_assoc($result)) {
        $row['quantity'] = $_SESSION['cart'][$row['id']];
        $cartItems[] = $row;
    }
}

function calculateTotal($cartItems) {
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Table with data
function InvoiceTable($header, $data) {
    // Header
    foreach($header as $col)
        $this->Cell(40,7,$col,1);
    $this->Ln();
    // Data
    foreach($data as $row) {
        $this->Cell(40,6,$row['name'],1); // Nama Produk
        $this->Cell(40,6,$row['price'],1); // Harga
        $this->Cell(40,6,$row['quantity'],1); // Jumlah
        $this->Cell(40,6,$row['price'] * $row['quantity'],1); // Total
        $this->Ln();
    }
}


// Tutup koneksi ke database
pg_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
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
</head>
<body>
    <div class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Keranjang Belanja</h2>
        <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-lg">
            <?php if (!empty($cartItems)): ?>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2">Nama Produk</th>
                            <th class="py-2">Harga</th>
                            <th class="py-2">Jumlah</th>
                            <th class="py-2">Total</th>
                            <th class="py-2">Hapus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($item['price']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></td>
                                <td class="border px-4 py-2">
                                    <form action="hapus.php" method="POST">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="mt-6">
                    <p class="font-bold">Total Belanja: Rp <?php echo htmlspecialchars(calculateTotal($cartItems)); ?></p>
                    <div class="mt-6">
                        <a href="dashboard_user.php" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-700">Lanjut Belanja</a>
                        <a href="checkout.php" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">Checkout</a>
                    </div>
                </div>
            <?php else: ?>
                <p>Keranjang belanja Anda kosong.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
