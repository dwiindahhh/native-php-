<?php
session_start();
require 'config.php';

// Buka koneksi ke database
$conn = opendb();

// Periksa apakah user sudah login
if (!isset($_SESSION['user_is_logged_in']) || $_SESSION['user_is_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Ambil ID buah yang akan diedit
$id = $_GET['id'];

// Ambil data buah berdasarkan ID
$query = "SELECT * FROM products WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));
$product = pg_fetch_assoc($result);

// Periksa apakah formulir sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Update data buah di database
    $updateQuery = "UPDATE products SET name = $1, price = $2, stock = $3 WHERE id = $4";
    $result = pg_query_params($conn, $updateQuery, array($name, $price, $stock, $id));

    if (!$result) {
        die("Error in SQL query: " . pg_last_error());
    }

    // Redirect ke halaman utama
    header('Location: dashboard_pegawai.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buah</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container mx-auto mt-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Edit Buah</h2>
        <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-lg">
            <form action="edit.php?id=<?php echo htmlspecialchars($id); ?>" method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-bold mb-2">Nama Buah:</label>
                    <input type="text" name="name" id="name" class="w-full p-2 border border-gray-300 rounded" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="mb-4">
                    <label for="price" class="block text-gray-700 font-bold mb-2">Harga:</label>
                    <input type="text" name="price" id="price" class="w-full p-2 border border-gray-300 rounded" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
                <div class="mb-4">
                    <label for="stock" class="block text-gray-700 font-bold mb-2">Stok:</label>
                    <input type="text" name="stock" id="stock" class="w-full p-2 border border-gray-300 rounded" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Update Buah</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
// Tutup koneksi ke database
pg_close($conn);
?>
