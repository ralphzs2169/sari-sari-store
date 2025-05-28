<?php
session_start();

require_once "../config/database.php";
require_once "../models/ProductModel.php";
require_once "../models/categoryModel.php";
require_once "../models/unitModel.php";

$db = new Database();
$conn = $db->getConnection();
$category = new CategoryModel($conn);
$unit = new UnitModel($conn);
$product = new ProductModel($conn, $category, $unit);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $name = trim($_POST['name'] ?? '');
        $category_id = intval($_POST['category_id'] ?? 0);
        $unit_id = intval($_POST['unit_id'] ?? 0);
        $cost_price = floatval($_POST['cost_price'] ?? 0);
        $selling_price = floatval($_POST['selling_price'] ?? 0);
        $quantity_in_stock = intval($_POST['quantity_in_stock'] ?? 0);

        error_log("ADMIN ID ===== " .  $_SESSION['admin_id']);
        // Basic validation
        if (empty($name)) {
            $_SESSION['product_error'] = "Product name is required.";
        } elseif ($category_id <= 0) {
            $_SESSION['product_error'] = "Please select a valid category.";
        } elseif ($unit_id <= 0) {
            $_SESSION['product_error'] = "Please select a valid unit.";
        } else {
            $imagePath = null;
            error_log("File upload info: " . print_r($_FILES['product_image'], true));
            // Handle image upload if file is submitted
            if (!empty($_FILES['product_image']['name'])) {
                $uploadsDir = __DIR__ . '/../assets/images/products/';
                error_log("Uploads Dir: " . realpath($uploadsDir));
                $webPathPrefix = '/sari-sari-store/assets/images/products/';

                // Ensure directory exists
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0777, true);
                }

                $fileName = uniqid('prod_') . '_' . basename($_FILES['product_image']['name']);
                $targetPath = $uploadsDir . $fileName;

                // Move uploaded file
                error_log("Upload directory: $uploadsDir");
                error_log("Target file path: $targetPath");
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
                    $imagePath = $webPathPrefix . $fileName;
                    error_log("Image successfully uploaded to $targetPath");
                } else {
                    error_log("Failed to move uploaded file to $targetPath");
                    $_SESSION['product_error'] = "Image upload failed.";
                    header("Location: /sari-sari-store/views/adminPanel/index.php?section=products");
                    exit;
                }
            }

            // Call create method on ProductModel
            $product->create($name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock, $imagePath);
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
        $currentImagePath = $_POST['current_image_path'] ?? null;

        if ($id && !empty($name) && $category_id > 0 && $unit_id > 0) {
            $imagePath = $currentImagePath;  // Default to current image path

            // Check if new image file uploaded
            if (!empty($_FILES['product_image']['name'])) {
                $uploadsDir = __DIR__ . '/../assets/images/products/';
                $webPathPrefix = '/sari-sari-store/assets/images/products/';

                // Ensure directory exists
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0777, true);
                }

                $fileName = uniqid('prod_') . '_' . basename($_FILES['product_image']['name']);
                $targetPath = $uploadsDir . $fileName;

                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
                    $imagePath = $webPathPrefix . $fileName;

                    // Optionally: delete old image file if exists and different from default
                    if ($currentImagePath && file_exists(__DIR__ . '/../' . ltrim($currentImagePath, '/'))) {
                        unlink(__DIR__ . '/../' . ltrim($currentImagePath, '/'));
                    }
                } else {
                    $_SESSION['product_error'] = "Image upload failed.";
                    header("Location: /sari-sari-store/views/adminPanel/index.php?section=products");
                    exit;
                }
            }

            // Call update with the image path
            $product->update($id, $name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock, $imagePath);
            $_SESSION['success'] = "Product updated successfully.";
        } else {
            $_SESSION['product_error'] = "Please fill all required fields correctly.";
        }

        $currentSection = $_POST['current_section'] ?? 'products';
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=products");
        exit;


    case 'delete':
        $id = intval($_GET['id'] ?? 0);
        $product_name = trim($_GET['name'] ?? '');

        if ($id > 0) {
            $product->delete($id,  $product_name);
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
