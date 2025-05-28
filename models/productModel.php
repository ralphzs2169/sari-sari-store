<?php

// Check if admin_id is set in the session. If not, redirect to login page.
if (!isset($_SESSION['admin_id'])) {
    header('Location: /sari-sari-store/views/login.php');
    exit();
}

require_once "categoryModel.php";
require_once "unitModel.php";

$db = new Database();
$conn = $db->getConnection();


class ProductModel
{
    private $conn;
    private $category;
    private $unit;

    public function __construct($db, $categoryModel, $unitModel)
    {
        $this->conn = $db;
        $this->category = $categoryModel;
        $this->unit = $unitModel;
    }
    public function create($name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock, $imagePath)
    {
        $this->conn->beginTransaction();

        try {
            // Call CreateProduct SP
            $stmt = $this->conn->prepare("CALL CreateProduct(:name, :category_id, :unit_id, :cost_price, :selling_price, :quantity_in_stock, :imagePath)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':unit_id', $unit_id, PDO::PARAM_INT);
            $stmt->bindParam(':cost_price', $cost_price);
            $stmt->bindParam(':selling_price', $selling_price);
            $stmt->bindParam(':quantity_in_stock', $quantity_in_stock, PDO::PARAM_INT);
            $stmt->bindParam(':imagePath', $imagePath);
            $stmt->execute();

            // Get product_id returned by SP
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $product_id = $row['product_id'] ?? 0;
            $stmt->closeCursor();

            if ($product_id === 0) {
                throw new Exception("Failed to retrieve product ID.");
            }

            // Call CreateActivityLog SP
            $activityStmt = $this->conn->prepare("CALL CreateActivityLog(:activity_type, :description)");
            $activity_type = 'new_product_added';
            $description = $name . " - " . $quantity_in_stock . " pieces added to inventory by " . $_SESSION['admin_username'];
            $activityStmt->bindParam(':activity_type', $activity_type);
            $activityStmt->bindParam(':description', $description);
            $activityStmt->execute();
            $activityStmt->closeCursor();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Product creation failed: " . $e->getMessage());
            return false;
        }
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("CALL GetAllProducts()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetProductCounts()
    {
        $stmt = $this->conn->prepare("CALL GetProductCounts()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($product_id)
    {
        $stmt = $this->conn->prepare("CALL GetProductById(:product_id)");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($product_id, $name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock, $imagePath = null, $skipLogging = false)
    {
        try {
            $this->conn->beginTransaction();

            $original = $this->getById($product_id);
            if (!$original) {
                throw new Exception("Product not found");
            }

            if ($quantity_in_stock <= 0) {
                $quantity_in_stock = 0;
            }

            // Update product fields except image via stored procedure
            $stmt = $this->conn->prepare("CALL UpdateProduct(:product_id, :name, :category_id, :unit_id, :cost_price, :selling_price, :quantity_in_stock, :image_path)");
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':unit_id', $unit_id, PDO::PARAM_INT);
            $stmt->bindParam(':cost_price', $cost_price);
            $stmt->bindParam(':selling_price', $selling_price);
            $stmt->bindParam(':quantity_in_stock', $quantity_in_stock, PDO::PARAM_INT);
            $stmt->bindParam(':image_path', $imagePath);
            $stmt->execute();

            // Helper to log activity (reuse existing closure)
            if (!$skipLogging) {
                $logActivity = function ($type, $desc) {
                    $stmt = $this->conn->prepare("CALL CreateActivityLog(:activity_type, :description)");
                    $stmt->bindParam(':activity_type', $type);
                    $stmt->bindParam(':description', $desc);
                    $stmt->execute();
                };

                // If image path is provided and different from original, update separately
                if ($imagePath !== null && $imagePath !== $original['image_path']) {
                    $stmtImage = $this->conn->prepare("UPDATE products SET image_path = :image_path WHERE product_id = :product_id");
                    $stmtImage->bindParam(':image_path', $imagePath);
                    $stmtImage->bindParam(':product_id', $product_id, PDO::PARAM_INT);
                    $stmtImage->execute();

                    // Log image update activity

                    $logActivity('product_image_updated', "$name: Product image updated by " . $_SESSION['admin_username']);
                }



                // (Existing logs for other changes...)

                if ($original['name'] !== $name) {
                    $logActivity('product_renamed', "Renamed product '{$original['name']}' to '$name' by " . $_SESSION['admin_username']);
                }

                $oldCategoryName = $this->category->getCategoryNameById($original['category_id']);
                $newCategoryName = $this->category->getCategoryNameById($category_id);

                if ($original['category_id'] != $category_id) {
                    $logActivity('product_category_changed', "$name: Category changed from '$oldCategoryName' to '$newCategoryName' by " . $_SESSION['admin_username']);
                }

                $oldUnitName = $this->unit->getUnitNameById($original['unit_id']);
                $newUnitName = $this->unit->getUnitNameById($unit_id);

                if ($original['unit_id'] != $unit_id) {
                    $logActivity('product_unit_changed', "$name: Unit changed from '$oldUnitName' to '$newUnitName' by " . $_SESSION['admin_username']);
                }

                if ($original['cost_price'] != $cost_price) {
                    $old = number_format($original['cost_price'], 2);
                    $new = number_format($cost_price, 2);
                    $desc = "$name: Cost price changed from ₱$old to ₱$new by " . $_SESSION['admin_username'];
                    $logActivity('cost_price_updated', $desc);
                }

                if ($original['selling_price'] != $selling_price) {
                    $old = number_format($original['selling_price'], 2);
                    $new = number_format($selling_price, 2);
                    $desc = "$name: Selling price changed from ₱$old to ₱$new by " . $_SESSION['admin_username'];
                    $logActivity('selling_price_updated', $desc);
                }

                if ($original['quantity_in_stock'] != $quantity_in_stock) {
                    if ($quantity_in_stock == 0) {
                        $logActivity('out_of_stock', "$name: 0 pieces remaining (Please restock)");
                    } elseif ($quantity_in_stock <= 5) {
                        $logActivity('low_stock_alert', "$name: Only $quantity_in_stock pieces remaining");
                    }

                    if ($quantity_in_stock > $original['quantity_in_stock']) {
                        $restockedQty = $quantity_in_stock - $original['quantity_in_stock'];
                        $logActivity('restocked', "$name: Restocked with $restockedQty piece(s) by " . $_SESSION['admin_username']);
                    } else {
                        $logActivity('stock_updated', "$name: Stock adjusted to $quantity_in_stock by " . $_SESSION['admin_username']);
                    }
                }

                // Always log a general update
                $logActivity('product_updated', "$name: Updated by " . $_SESSION['admin_username']);
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Update product failed: " . $e->getMessage());
            return false;
        }
    }


    public function delete($product_id, $product_name)
    {
        try {
            $this->conn->beginTransaction();

            // Log activity BEFORE deleting the product
            $activity_type = 'product_deleted';
            $description = "$product_name - Removed from inventory by " . $_SESSION['admin_username'];
            $activityStmt = $this->conn->prepare("CALL CreateActivityLog(:activity_type, :description)");
            $activityStmt->bindParam(':activity_type', $activity_type);
            $activityStmt->bindParam(':description', $description);
            $activityStmt->execute();

            // Delete product AFTER logging
            $stmt = $this->conn->prepare("CALL DeleteProduct(:product_id)");
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Delete product failed: " . $e->getMessage());
            return false;
        }
    }
}
