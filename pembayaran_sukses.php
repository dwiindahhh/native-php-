<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pembayaran</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mx-auto mt-10">
        <div class="bg-white bg-opacity-90 p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-gray-800">Form Pembayaran</h2>
            <form action="sukses.php" method="POST">
                <div class="mt-4">
                    <label for="nama" class="block text-gray-700">Nama:</label>
                    <input type="text" id="nama" name="nama" required class="border border-gray-300 p-2 rounded w-full">
                </div>
                <div class="mt-4">
                    <label for="email" class="block text-gray-700">Email:</label>
                    <input type="email" id="email" name="email" required class="border border-gray-300 p-2 rounded w-full">
                </div>
                <div class="mt-4">
                    <button type="submit" class="bg-blue-500 text-white p-2 rounded">Bayar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>


