<?php
class ProductModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock)
    {
        $stmt = $this->conn->prepare("CALL CreateProduct(:name, :category_id, :unit_id, :cost_price, :selling_price, :quantity_in_stock)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':unit_id', $unit_id, PDO::PARAM_INT);
        $stmt->bindParam(':cost_price', $cost_price);
        $stmt->bindParam(':selling_price', $selling_price);
        $stmt->bindParam(':quantity_in_stock', $quantity_in_stock, PDO::PARAM_INT);
        return $stmt->execute();
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

    public function update($product_id, $name, $category_id, $unit_id, $cost_price, $selling_price, $quantity_in_stock)
    {
        $stmt = $this->conn->prepare("CALL UpdateProduct(:product_id, :name, :category_id, :unit_id, :cost_price, :selling_price, :quantity_in_stock)");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':unit_id', $unit_id, PDO::PARAM_INT);
        $stmt->bindParam(':cost_price', $cost_price);
        $stmt->bindParam(':selling_price', $selling_price);
        $stmt->bindParam(':quantity_in_stock', $quantity_in_stock, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($product_id)
    {
        $stmt = $this->conn->prepare("CALL DeleteProduct(:product_id)");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
