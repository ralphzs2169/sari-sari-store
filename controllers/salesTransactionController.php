<?php
session_start();

require_once "../models/salesTransactionModel.php";
require_once "../models/saleItemModel.php";

$salesTransaction = new SalesTransactionModel();
$saleItemModel = new SaleItemModel();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        // Support JSON payload (AJAX)
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        if ($data) {
            $customer_id = !empty($data['customer_id']) ? intval($data['customer_id']) : null;
            $admin_id = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : 0;
            $total_amount = isset($data['total_amount']) ? floatval($data['total_amount']) : 0;
            $payment_method = $data['payment_method'] ?? 'cash';
            // $cart = $data['cart'] ?? [];
            if ($admin_id > 0 && $total_amount > 0) {
                $sale_id = $salesTransaction->create($customer_id, $admin_id, $total_amount, $payment_method);
                // TODO: Save cart items to a sale_items table if needed
                echo json_encode(['success' => true, 'sale_id' => $sale_id]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid sale data.']);
                exit;
            }
        } else {
            // Fallback to form POST (no AJAX)
            $customer_id = !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
            $admin_id = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : 0;
            $total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
            $payment_method = $_POST['payment_method'] ?? 'cash';
            $cart = isset($_POST['cart']) ? json_decode($_POST['cart'], true) : [];
            if ($admin_id > 0 && $total_amount > 0) {
                $sale_id = $salesTransaction->create($customer_id, $admin_id, $total_amount, $payment_method);
                // Save each cart item to sale_items
                if ($sale_id && is_array($cart)) {
                    foreach ($cart as $item) {
                        $product_id = isset($item['id']) ? intval($item['id']) : 0;
                        $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
                        $price = isset($item['price']) ? floatval($item['price']) : 0;
                        if ($product_id > 0 && $quantity > 0) {
                            $saleItemModel->create($sale_id, $product_id, $quantity, $price);
                        }
                    }
                }
                $_SESSION['success'] = "Sale transaction recorded successfully.";
                // Optionally clear cart in session or show receipt
                header("Location: /sari-sari-store/views/adminPanel/index.php?section=sales");
                exit;
            } else {
                $_SESSION['sales_error'] = "Invalid sale data.";
                header("Location: /sari-sari-store/views/adminPanel/index.php?section=sales");
                exit;
            }
        }

    case 'delete':
        // Optional: implement if you want to allow deleting sales
        break;

    case 'view':
        $sale_id = intval($_GET['sale_id'] ?? 0);
        if ($sale_id > 0) {
            $sale = $salesTransaction->getById($sale_id);
            // You can return as JSON or render a view as needed
            header('Content-Type: application/json');
            echo json_encode($sale);
            exit;
        }
        break;

    default:
        // Optionally, list all sales or redirect
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=sales");
        exit;
}
