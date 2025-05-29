<?php
// models/salesTransactionModel.php

class SalesTransactionModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function create($admin_id, $total_amount, $payment_method = 'cash')
    {
        error_log("ADMIN ID: " . $admin_id);
        $stmt = $this->conn->prepare("CALL CreateTransaction(:admin_id, :total_amount, :payment_method, @sale_id)");
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':total_amount', $total_amount);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->execute();

        // Fetch the output parameter
        $result = $this->conn->query("SELECT @sale_id AS sale_id")->fetch(PDO::FETCH_ASSOC);
        return $result['sale_id'];
    }

    public function getById($sale_id)
    {
        $stmt = $this->conn->prepare("CALL GetTransactionById(:sale_id)");
        $stmt->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("CALL GetAllTransactions()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByAdmin($admin_id)
    {
        $stmt = $this->conn->prepare("CALL GetTransactionsByAdmin(:admin_id)");
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTodaySalesTotal()
    {
        $stmt = $this->conn->prepare("CALL GetTodaySalesTotal()");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? floatval($row['total']) : 0.0;
    }

    public function countTodayTransactions()
    {
        $stmt = $this->conn->prepare("CALL GetTodayTransactionCount()");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? intval($row['total']) : 0;
    }

    public function getSalesByCategory()
    {
        $stmt = $this->conn->prepare("CALL GetSalesByCategory()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function voidTransaction($sale_id)
    {
        $stmt = $this->conn->prepare("CALL VoidTransaction(:sale_id)");
        $stmt->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getTopProducts()
    {
        $stmt = $this->conn->prepare("CALL GetTopProducts()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
