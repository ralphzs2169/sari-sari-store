<?php
class CategoryModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($name, $description)
    {
        $stmt = $this->conn->prepare("CALL CreateCategory(:name, :description)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
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

    public function update($category_id, $name, $description)
    {
        $stmt = $this->conn->prepare("CALL UpdateCategory(:category_id, :name, :description)");
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function delete($category_id)
    {
        $stmt = $this->conn->prepare("CALL DeleteCategory(:category_id)");
        $stmt->bindParam(':category_id', $category_id);
        return $stmt->execute();
    }
}
