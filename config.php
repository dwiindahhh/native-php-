<?php
function opendb() {
    $host = 'localhost';
    $dbname = 'web5';
    $user = 'postgres';
    $password = 'indahcantik';

    $conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");
    if (!$conn) {
        echo "An error occurred.\n";
        exit;
    }
    return $conn;
}
?>
