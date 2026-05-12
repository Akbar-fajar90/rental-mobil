<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "db_rental_mobil");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->query("ALTER TABLE t_pelanggan ADD COLUMN reset_token VARCHAR(255) NULL");
$mysqli->query("ALTER TABLE t_pelanggan ADD COLUMN reset_expires DATETIME NULL");

echo "Success";
$mysqli->close();
