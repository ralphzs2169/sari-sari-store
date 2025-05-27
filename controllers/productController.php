<?php
session_start();

require_once "../config/database.php";
require_once "../models/ProductModel.php";

$db = new Database();
$conn = $db->getConnection();
$product = new ProductModel($conn);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $name = trim($_POST['name'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $unit_id = intval($_POST['unit_id'] ?? 0);
        $cost_price = floatval($_POST['cost_price'] ?? 0);
        $selling_price = floatval($_POST['selling_price'] ?? 0);
        $quantity_in_stock = intval($_POST['quantity_in_stock'] ?? 0);

        // Basic validation
        if (empty($name)) {
            $_SESSION['product_error'] = "Product name is required.";
        } elseif ($category_id <= 0) {
            $_SESSION['product_error'] = "Please select a valid category.";
        } elseif ($unit_id <= 0) {
            $_SESSION['product_error'] = "Please select a valid unit.";
        } else {
            // Call create method on ProductModel
            $product->create($name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock);
            $_SESSION['success'] = "Product added successfully.";
        }

        $currentSection = $_POST['current_section'] ?? 'products';
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=products");
        exit;

    case 'update':
        $id = intval($_POST['editProductId'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $unit_id = intval($_POST['unit_id'] ?? 0);
        $cost_price = floatval($_POST['cost_price'] ?? 0);
        $selling_price = floatval($_POST['selling_price'] ?? 0);
        $quantity_in_stock = intval($_POST['quantity_in_stock'] ?? 0);

        if ($id && !empty($name) && $category_id > 0 && $unit_id > 0) {
            $product->update($id, $name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock);
            $_SESSION['success'] = "Product updated successfully.";
        } else {
            $_SESSION['product_error'] = "Please fill all required fields correctly.";
        }

        $currentSection = $_POST['current_section'] ?? 'products';
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=products");
        exit;

    case 'delete':
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $product->delete($id);
            $_SESSION['success'] = "Product deleted.";
        }
        $currentSection = $_GET['current_section'] ?? 'products';
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=products");
        exit;

    default:
        // Optionally redirect or show error if action is unknown
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=products");
        exit;
}
