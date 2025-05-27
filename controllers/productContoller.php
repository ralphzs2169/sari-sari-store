<?php
session_start();

require_once "../config/database.php";
require_once "../models/Product.php";

$db = new Database();
$conn = $db->getConnection();
$product = new ProductModel($conn);

$action = $_GET['action'] ?? '';

if ($action === 'create') {
    $name = trim($_POST['name'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $unit_id = intval($_POST['unit_id'] ?? 0);
    $cost_price = floatval($_POST['cost_price'] ?? 0);
    $selling_price = floatval($_POST['selling_price'] ?? 0);
    $quantity_in_stock = intval($_POST['quantity_in_stock'] ?? 0);

    if ($product->create($name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock)) {
        $_SESSION['success'] = "Product created successfully.";
    } else {
        $_SESSION['error'] = "Failed to create product.";
    }

    header("Location: ../views/product-add.php");
    exit;
}
