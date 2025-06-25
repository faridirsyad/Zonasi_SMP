<?php
include 'admin_only.php';
include 'koneksi.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM informasi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin_dashboard.php?success_delete=1");
exit;
