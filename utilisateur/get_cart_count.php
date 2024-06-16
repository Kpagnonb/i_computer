<?php
session_start();

$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

echo json_encode(['cartCount' => $cartCount]);
?>
