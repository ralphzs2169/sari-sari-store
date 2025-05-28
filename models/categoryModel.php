<?php

// Check if admin_id is set in the session. If not, redirect to login page.
if (!isset($_SESSION['admin_id'])) {
    header('Location: /sari-sari-store/views/login.php');
    exit();
}

class CategoryModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($name, $description)
    {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("CALL CreateCategory(:name, :description)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->execute();

            $logStmt = $this->conn->prepare("CALL CreateActivityLog(:type, :desc)");
            $type = 'new_category_added';
            $desc = "$name: New category created by " . $_SESSION['admin_username'];
            $logStmt->bindParam(':type', $type);
            $logStmt->bindParam(':desc', $desc);
            $logStmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Category creation failed: " . $e->getMessage());
            return false;
        }
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("CALL GetAllCategories()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($category_id)
    {
        $stmt = $this->conn->prepare("CALL GetCategoryById(:category_id)");
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategoryNameById($category_id)
    {
        $stmt = $this->conn->prepare("CALL GetCategoryById(:category_id)");
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        return $category ? $category['name'] : 'Unknown Category';
    }


    public function update($category_id, $name, $description)
    {
        try {
            $this->conn->beginTransaction();

            $original = $this->getById($category_id);
            if (!$original) throw new Exception("Category not found.");

            $stmt = $this->conn->prepare("CALL UpdateCategory(:category_id, :name, :description)");
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->execute();

            // Activity logging
            $logStmt = $this->conn->prepare("CALL CreateActivityLog(:type, :desc)");

            if ($original['name'] !== $name) {
                $type = 'category_renamed';
                $desc = "Category renamed from '{$original['name']}' to '$name' by " . $_SESSION['admin_username'];
                $logStmt->bindParam(':type', $type);
                $logStmt->bindParam(':desc', $desc);
                $logStmt->execute();
            }

            if ($original['description'] !== $description) {
                $type = 'category_description_updated';
                $desc = "$name: Description updated by " . $_SESSION['admin_username'];
                $logStmt->bindParam(':type', $type);
                $logStmt->bindParam(':desc', $desc);
                $logStmt->execute();
            }

            $type = 'category_updated';
            $desc = "$name: Category updated by " . $_SESSION['admin_username'];
            $logStmt->bindParam(':type', $type);
            $logStmt->bindParam(':desc', $desc);
            $logStmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Category update failed: " . $e->getMessage());
            return false;
        }
    }

    public function delete($category_id)
    {
        try {
            $this->conn->beginTransaction();

            $category = $this->getById($category_id);
            if (!$category) throw new Exception("Category not found.");

            $stmt = $this->conn->prepare("CALL DeleteCategory(:category_id)");
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();

            $logStmt = $this->conn->prepare("CALL CreateActivityLog(:type, :desc)");
            $type = 'category_deleted';
            $desc = "{$category['name']}: Category deleted by " . $_SESSION['admin_username'];
            $logStmt->bindParam(':type', $type);
            $logStmt->bindParam(':desc', $desc);
            $logStmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Delete category failed: " . $e->getMessage());
            return false;
        }
    }
}
