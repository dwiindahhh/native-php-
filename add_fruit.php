<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

require 'config.php';

// Fungsi untuk menambahkan buah
function tambahBuah($pdo, $id, $nama, $harga) {
    $query = "INSERT INTO products (id, name, price) VALUES (:id, :name, :price)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':id' => $id, ':name' => $nama, ':price' => $harga));
}



// Buka koneksi ke database
$pdo = opendb();

// Jika ada data POST (form telah disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_buah = $_POST['id_buah'];
    $nama_buah = $_POST['nama_buah'];
    $harga_buah = $_POST['harga_buah'];

    // Panggil fungsi untuk menambahkan buah ke dalam database
    tambahBuah($pdo, $id_buah, $nama_buah, $harga_buah);

    // Redirect kembali ke halaman dashboard setelah menambahkan buah
    header('Location: dashboard.php?success=1');
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
<body>
    <div class="container mx-auto mt-10">
        <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Tambah Buah Baru</h2>
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="bg-green-200 text-green-800 px-3 py-2 mb-4 rounded-md">Penambahan buah berhasil!</div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label for="nama_buah" class="block text-gray-700 font-bold">Nama Buah:</label>
                    <input type="text" id="nama_buah" name="nama_buah" class="form-input mt-1 block w-full" required>
                </div>
                <div class="mb-4">
                    <label for="harga_buah" class="block text-gray-700 font-bold">Harga Buah:</label>
                    <input type="number" id="harga_buah" name="harga_buah" class="form-input mt-1 block w-full" required>
                </div>
                <div class="mb-4">
                    <label for="id_buah" class="block text-gray-700 font-bold">ID Buah:</label>
                    <input type="text" id="id_buah" name="id_buah" class="form-input mt-1 block w-full" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700">Tambah Buah</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
// Tutup koneksi ke database
closedb($pdo);
?>
