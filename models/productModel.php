<?php
class ProductModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock, $admin_id)
    {
        $this->conn->beginTransaction();

        try {
            // Call CreateProduct SP
            $stmt = $this->conn->prepare("CALL CreateProduct(:name, :category_id, :unit_id, :cost_price, :selling_price, :quantity_in_stock)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':unit_id', $unit_id, PDO::PARAM_INT);
            $stmt->bindParam(':cost_price', $cost_price);
            $stmt->bindParam(':selling_price', $selling_price);
            $stmt->bindParam(':quantity_in_stock', $quantity_in_stock, PDO::PARAM_INT);
            $stmt->execute();

            // Get product_id returned by SP
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $product_id = $row['product_id'] ?? 0;
            $stmt->closeCursor();

            if ($product_id === 0) {
                throw new Exception("Failed to retrieve product ID.");
            }

            // Call CreateActivityLog SP
            $activityStmt = $this->conn->prepare("CALL CreateActivityLog(:admin_id, :activity_type, :description)");
            $activity_type = 'new_product_added';
            $description = $name . " - " . $quantity_in_stock . " pieces added to inventory by " . $_SESSION['admin_username'];
            $activityStmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
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

    public function update($product_id, $name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock, $admin_id)
    {
        try {
            $this->conn->beginTransaction();

            // Update product
            $stmt = $this->conn->prepare("CALL UpdateProduct(:product_id, :name, :category_id, :unit_id, :cost_price, :selling_price, :quantity_in_stock)");
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindParam(':unit_id', $unit_id, PDO::PARAM_INT);
            $stmt->bindParam(':cost_price', $cost_price);
            $stmt->bindParam(':selling_price', $selling_price);
            $stmt->bindParam(':quantity_in_stock', $quantity_in_stock, PDO::PARAM_INT);
            $stmt->execute();

            // Log activity
            $activity_type = 'product_updated';
            $description = "Updated product: $name";
            $activityStmt = $this->conn->prepare("CALL CreateActivityLog(:admin_id, :activity_type, :description)");
            $activityStmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
            $activityStmt->bindParam(':activity_type', $activity_type);
            $activityStmt->bindParam(':description', $description);
            $activityStmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Update product failed: " . $e->getMessage());
            return false;
        }
    }

    public function delete($product_id, $product_name, $admin_id)
    {
        try {
            $this->conn->beginTransaction();

            // Log activity BEFORE deleting the product
            $activity_type = 'product_deleted';
            $description = "$product_name - Removed from inventory by " . $_SESSION['admin_username'];
            $activityStmt = $this->conn->prepare("CALL CreateActivityLog(:admin_id, :activity_type, :description)");
            $activityStmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
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
