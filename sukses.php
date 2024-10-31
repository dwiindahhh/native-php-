<?php
session_start();

if (!isset($_SESSION['order_info'])) {
    header("Location: checkout.php");
    exit;
}

$nama = $_SESSION['order_info']['nama_pemesan'];
$email = isset($_SESSION['user_info']['email']) ? $_SESSION['user_info']['email'] : 'Tidak ada email';

// Hapus informasi pemesanan setelah ditampilkan
unset($_SESSION['order_info']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Sukses</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://images.pexels.com/photos/9386052/pexels-photo-9386052.jpeg'); /* Ganti 'background.jpg' dengan nama file gambar latar belakang Anda */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .button {
            display: inline-block;
            padding: 0.75rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            margin-right: 0.5rem;
        }

        .bg-blue {
            background-color: #3B82F6;
            color: white;
        }

        .bg-blue:hover {
            background-color: #2563EB;
        }

        .bg-red {
            background-color: #EF4444;
            color: white;
        }

        .bg-red:hover {
            background-color: #DC2626;
        }

        .bg-green {
            background-color: #10B981;
            color: white;
        }

        .bg-green:hover {
            background-color: #059669;
        }
    </style>
</head>
<body>
    <div class="container mx-auto mt-10">
        <div class="card">
            <h2 class="text-3xl font-bold text-gray-800">Pembayaran Sukses</h2>
            <p class="text-gray-700 mt-4">Terima kasih, <?php echo htmlspecialchars($nama); ?>. Pembayaran Anda telah berhasil diproses.</p>
            <div class="mt-6">
                <a href="download_struk.php" class="button bg-blue">Download struk</a>
                <a href="logout.php" class="button bg-red">Logout</a>
                <a href="dashboard_user.php" class="button bg-green">Lanjut Belanja</a>
            </div>
        </div>
    </div>
</body>
</html>
