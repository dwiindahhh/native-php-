<?php
session_start();

require 'config.php';

// Buka koneksi ke database
$connection = opendb();

// Inisialisasi respons default
$response = array(
    'success' => false,
    'error' => 'Gagal menambahkan buah ke dalam keranjang'
);

// Periksa apakah ID produk telah diterima
if (isset($_POST['id'])) {
    $productId = $_POST['id'];

    // Ambil informasi produk dari database
    $query = "SELECT * FROM products WHERE id = $1";
    $result = pg_query_params($connection, $query, array($productId));
    $product = pg_fetch_assoc($result);

    if ($product) {
        // Periksa apakah stok tersedia
        if ($product['stock'] > 0) {
            // Kurangi stok produk di database
            $newStock = $product['stock'] - 1;
            $updateQuery = "UPDATE products SET stock = $1 WHERE id = $2";
            $updateResult = pg_query_params($connection, $updateQuery, array($newStock, $productId));

            if ($updateResult) {
                // Tambahkan produk ke dalam keranjang
                if (!isset($_SESSION['cart'][$productId])) {
                    $_SESSION['cart'][$productId] = 1;
                } else {
                    $_SESSION['cart'][$productId]++;
                }

                // Update respons
                $response = array(
                    'success' => true,
                    'message' => 'Produk telah dimasukkan ke dalam keranjang',
                    'cart_count' => array_sum($_SESSION['cart']),
                    'new_stock' => $newStock
                );
            } else {
                $response['error'] = 'Gagal mengupdate stok produk';
            }
        } else {
            $response['error'] = 'Stok buah habis';
        }
    } else {
        $response['error'] = 'Produk tidak ditemukan';
    }
}

// Tutup koneksi ke database
pg_close($connection);

// Kirim respons dalam format JSON
echo json_encode($response);
?>
