<?php
session_start();

require_once "../config/database.php";
require_once "../models/salesTransactionModel.php";
require_once "../models/saleItemModel.php";
require_once "../models/productModel.php";
require_once "../models/categoryModel.php";
require_once "../models/unitModel.php";
require_once "../models/activityLogModel.php";


$salesTransaction = new SalesTransactionModel();
$saleItemModel = new SaleItemModel();

$activityLog = new ActivityLogModel($conn);
$categoryModel = new CategoryModel($conn);
$unitModel = new UnitModel($conn);

$productModel = new ProductModel($conn, $categoryModel, $unitModel);
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $customer_id = !empty($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
        $admin_id = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : 0;
        $total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
        $payment_method = $_POST['payment_method'] ?? 'cash';
        $cart = isset($_POST['cart']) ? json_decode($_POST['cart'], true) : [];
        if ($admin_id > 0 && $total_amount > 0) {
            $sale_id = $salesTransaction->create($admin_id, $total_amount, $payment_method);

            $productNames = [];
            foreach ($cart as $item) {
                $product_id = isset($item['id']) ? intval($item['id']) : 0;
                if ($product_id > 0) {
                    $product = $productModel->getById($product_id);
                    if ($product && isset($product['name'])) {
                        $productNames[] = $product['name'];
                    }
                }
            }

            // Custom formatting for list
            $productCount = count($productNames);
            if ($productCount > 1) {
                $lastItem = array_pop($productNames);
                $description = "Customer Purchased " . implode(", ", $productNames) . " and " . $lastItem;
            } else {
                $description = "Customer Purchased " . implode(", ", $productNames);
            }

            $activityLog->create("new_sale_transaction_completed", $description);
            // Save each cart item to sale_items
            if ($sale_id && is_array($cart)) {
                foreach ($cart as $item) {
                    $product_id = isset($item['id']) ? intval($item['id']) : 0;
                    $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
                    $price = isset($item['price']) ? floatval($item['price']) : 0;
                    if ($product_id > 0 && $quantity > 0) {
                        $saleItemModel->create($sale_id, $product_id, $quantity, $price);

                        // fetch product details to update stock
                        $product = $productModel->getById($product_id);
                        if ($product) {
                            $newStock = max(0, intval($product['quantity_in_stock']) - $quantity);

                            // Call update method with current product info, updated stock, and skip logging
                            $productModel->update(
                                $product_id,
                                $product['name'],
                                $product['category_id'],
                                $product['unit_id'],
                                $product['cost_price'],
                                $product['selling_price'],
                                $newStock,
                                $product['image_path'],
                                true // skipLogging = true
                            );
                        }
                    }
                }
            }
            $_SESSION['success'] = "Sale transaction recorded successfully.";
            // Optionally clear cart in session or show receipt
            header("Location: /sari-sari-store/views/adminPanel/index.php?section=pos");
            exit;
        } else {
            $_SESSION['sales_error'] = "Invalid sale data.";
            header("Location: /sari-sari-store/views/adminPanel/index.php?section=pos");
            exit;
        }
        break;


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

    case 'void':
        $sale_id = intval($_POST['sale_id'] ?? 0);
        if ($sale_id > 0) {
            // Get all sale items linked to this sale_id
            $saleItems = $saleItemModel->getBySaleId($sale_id);

            foreach ($saleItems as $item) {
                $product_id = $item['product_id'];
                $quantity_sold = $item['quantity'];  // Quantity sold in this sale

                // Fetch full product info by product_id
                $product = $productModel->getById($product_id);
                if ($product) {
                    $new_stock = $product['quantity_in_stock'] + $quantity_sold;

                    // Update product stock with all original details intact
                    $productModel->update(
                        $product_id,
                        $product['name'],
                        $product['category_id'],
                        $product['unit_id'],
                        $product['cost_price'],
                        $product['selling_price'],
                        $new_stock,
                        $product['image_path'] ?? null,
                        true  // skip logging
                    );
                }
            }

            // Void the sale transaction
            $result = $salesTransaction->voidTransaction($sale_id);

            if ($result) {
                $_SESSION['success'] = 'Transaction voided successfully.';
            } else {
                $_SESSION['error'] = 'Failed to void transaction.';
            }
        } else {
            $_SESSION['error'] = 'Invalid sale ID.';
        }
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=sales");
        exit;



    case 'top_products':
        $topProducts = $salesTransaction->getTopProducts();
        header('Content-Type: application/json');
        echo json_encode($topProducts);
        exit;

    case 'sales_by_category':
        $data = $salesTransaction->getSalesByCategory();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;

    case 'payment_method_breakdown':
        $data = $salesTransaction->getPaymentMethodBreakdown();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;

    case 'profit_performance':
        $range = $_GET['range'] ?? 'monthly';
        $start = $_GET['start'] ?? null;
        $end = $_GET['end'] ?? null;
        $data = $salesTransaction->getProfitPerformance($range, $start, $end);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;

    default:
        // Optionally, list all sales or redirect
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=sales");
        exit;
}
