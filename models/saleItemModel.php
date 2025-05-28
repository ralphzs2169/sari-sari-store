<?php
// models/saleItemModel.php
require_once "../config/database.php";

class SaleItemModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function create($sale_id, $product_id, $quantity, $price)
    {
        $stmt = $this->conn->prepare("CALL CreateSaleItem(:sale_id, :product_id, :quantity, :price, @new_id)");
        $stmt->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        $stmt->execute();

        // Fetch the output parameter
        $result = $this->conn->query("SELECT @new_id AS sale_item_id");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return $row['sale_item_id'];
    }

    public function getBySaleId($sale_id)
    {
        $stmt = $this->conn->prepare("CALL GetSaleItemsBySaleId(:sale_id)");
        $stmt->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
