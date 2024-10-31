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

// Ambil informasi pengguna jika ada
$userInfo = [];
if (isset($_SESSION['user_info'])) {
    $userInfo = $_SESSION['user_info'];
}

// Jika formulir checkout dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $namaPemesan = $_POST['nama_pemesan'];
    $alamat = $_POST['alamat'];
    $payment = $_POST['payment'];
    $pengiriman = $_POST['pengiriman'];

    // Simpan informasi pemesanan dan pengguna ke dalam sesi
    $_SESSION['order_info'] = [
        'nama_pemesan' => $namaPemesan,
        'alamat' => $alamat,
        'payment' => $payment,
        'pengiriman' => $pengiriman,
        'total' => calculateTotal($cartItems),
    ];

    // Redirect ke halaman pembayaran
    header("Location: pembayaran.php");
    exit;
}

// Tutup koneksi ke database
pg_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Checkout</h2>
        <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-lg">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="mb-4">
                    <label for="nama_pemesan" class="block text-gray-700 font-bold mb-2">Nama Pemesan:</label>
                    <input type="text" id="nama_pemesan" name="nama_pemesan" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo isset($userInfo['nama']) ? htmlspecialchars($userInfo['nama']) : ''; ?>">
                </div>
                <div class="mb-4">
                    <label for="alamat" class="block text-gray-700 font-bold mb-2">Alamat Lengkap:</label>
                    <textarea id="alamat" name="alamat" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo isset($userInfo['alamat']) ? htmlspecialchars($userInfo['alamat']) : ''; ?></textarea>
                </div>
                
                <!-- Rincian Pesanan -->
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Rincian Pesanan:</h3>
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2">Nama Produk</th>
                                <th class="py-2">Harga</th>
                                <th class="py-2">Jumlah</th>
                                <th class="py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($item['price']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <p class="font-bold">Total Belanja: Rp <?php echo htmlspecialchars(calculateTotal($cartItems)); ?></p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="payment" class="block text-gray-700 font-bold mb-2">Metode Pembayaran:</label>
                    <select id="payment" name="payment" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="transfer_bank">Transfer Bank</option>
                        <option value="ovo">OVO</option>
                        <option value="dana">Dana</option>
                        <option value="shoopepay">Shoopepay</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="pengiriman" class="block text-gray-700 font-bold mb-2">Metode Pengiriman:</label>
                    <select id="pengiriman" name="pengiriman" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="kurir">Kurir</option>
                        <option value="jnt_express">JNT Express</option>
                    </select>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Checkout</button>
                    <a href="dashboard_user.php" class="text-gray-600 font-bold hover:text-gray-800">Kembali ke halaman utama</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
