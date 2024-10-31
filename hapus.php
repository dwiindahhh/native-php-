<?php
session_start();

// Pastikan ID produk yang akan dihapus ada dalam permintaan POST
if (isset($_POST['id'])) {
    $productId = $_POST['id'];

    // Hapus produk dari keranjang belanja jika ada
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

// Redirect kembali ke halaman keranjang belanja
header("Location: cart.php");
exit;
?>
