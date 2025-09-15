<?php
include "config.php"; // koneksi ke database

// aktifkan error reporting supaya kalau ada error tidak hanya HTTP 500
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek apakah id ada
if (!isset($_GET['id'])) {
    die("ID tidak ditemukan!");
}

$id = intval($_GET['id']); // amankan id

// Ambil data shipment yang akan dihapus
$sql = "SELECT * FROM shipments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$shipment = $result->fetch_assoc();

if (!$shipment) {
    die("Data tidak ditemukan!");
}

// Simpan data ke tabel history dengan status Delete
$sql_insert_history = "INSERT INTO history
    (shipment_id, customer_id, spx, anter, sicepat, jnt, jne, jnt_cargo, jne_cargo, lazada, pos, id_express, total, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Delete')";

$stmt_insert = $conn->prepare($sql_insert_history);

// cek tipe kolom total, kalau INT pakai "i", kalau DECIMAL/DOUBLE pakai "d"
$stmt_insert->bind_param(
    "iiiiiiiiiiiii",
    $shipment['id'],
    $shipment['customer_id'],
    $shipment['spx'],
    $shipment['anter'],
    $shipment['sicepat'],
    $shipment['jnt'],
    $shipment['jne'],
    $shipment['jnt_cargo'],
    $shipment['jne_cargo'],
    $shipment['lazada'],
    $shipment['pos'],
    $shipment['id_express'],
    $shipment['total'] // ganti "d" kalau tipe kolom ini decimal/double
);
$stmt_insert->execute();

// Hapus shipment dari tabel utama
$stmt_delete = $conn->prepare("DELETE FROM shipments WHERE id = ?");
$stmt_delete->bind_param("i", $id);
$stmt_delete->execute();

// Redirect ke halaman utama
header("Location: index.php");
exit;
?>
