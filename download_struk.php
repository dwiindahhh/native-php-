<?php
session_start();

require 'config.php'; // Memanggil file config.php yang berisi fungsi opendb()

// Tentukan path ke fpdf.php
$fpdfPath = 'C:/XAAMP/htdocs/PAWEB2/fpdf/fpdf.php';

// Cek apakah file fpdf.php ada
if (!file_exists($fpdfPath)) {
    die('FPDF file not found at ' . $fpdfPath);
}

require $fpdfPath;

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

// Fungsi untuk menghitung total belanja
function calculateTotal($cartItems) {
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Tutup koneksi ke database
pg_close($connection);

class PDF extends FPDF {
    // Header
    function Header() {
        $this->SetFont('Arial','B',12);
        $this->Cell(0,10,'Invoice',0,1,'C');
        $this->Ln(10);
    }

    // Footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }

    // Table with data
    function InvoiceTable($header, $data) {
        // Header
        foreach($header as $col)
            $this->Cell(40,7,$col,1);
        $this->Ln();
        // Data
        foreach($data as $row) {
            $this->Cell(40,6,$row['name'],1);
            $this->Cell(40,6,$row['price'],1);
            $this->Cell(40,6,$row['quantity'],1);
            $this->Cell(40,6,$row['price'] * $row['quantity'],1);
            $this->Ln();
        }
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);

// Header table
$header = array('Nama Produk', 'Harga', 'Jumlah', 'Total');
$pdf->InvoiceTable($header, $cartItems);

// Total
$total = calculateTotal($cartItems);
$pdf->Ln();
$pdf->Cell(120,7,'Total Belanja',1);
$pdf->Cell(40,7,'Rp '.number_format($total),1);

$pdf->Output('D', 'Invoice.pdf');
?>
