<?php
include 'admin_only.php';
include 'koneksi.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM sekolah WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin_sekolah.php");
exit;
