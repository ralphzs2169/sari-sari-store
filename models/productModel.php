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
        $sql = "INSERT INTO products (name, category_id, unit_id, cost_price, selling_price, quantity_in_stock)
                VALUES (:name, :category_id, :unit_id, :cost_price, :selling_price, :quantity_in_stock)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->bindParam(':cost_price', $cost_price);
        $stmt->bindParam(':selling_price', $selling_price);
        $stmt->bindParam(':quantity_in_stock', $quantity_in_stock);
        return $stmt->execute();
    }
}
