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

    public function getTotalProfit()
    {
        $stmt = $this->conn->prepare("CALL GetTotalProfit()");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['total_profit'] : 0;
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
    public function getPaymentMethodBreakdown()
    {
        $stmt = $this->conn->prepare("CALL GetSalesByPaymentMethod");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProfitPerformance($range = 'weekly', $start = null, $end = null)
    {
        $stmt = $this->conn->prepare("CALL GetProfitPerformance(:range, :start, :end)");
        $stmt->bindParam(':range', $range, PDO::PARAM_STR);
        $stmt->bindParam(':start', $start);  // Will be NULL for weekly/monthly
        $stmt->bindParam(':end', $end);      // Will be NULL for weekly/monthly
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); // Allow calling another stored procedure afterward

        // Post-process for weekly or monthly for filling missing values (optional)
        if ($range === 'weekly') {
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $profitByDay = array_fill_keys($days, 0);
            foreach ($result as $row) {
                $profitByDay[$row['label']] = (float)$row['profit'];
            }
            $final = [];
            foreach ($days as $d) {
                $final[] = ['label' => $d, 'profit' => $profitByDay[$d]];
            }
            return $final;
        } elseif ($range === 'monthly') {
            // Determine week numbers in current month
            $first = new DateTime(date('Y-m-01'));
            $last = new DateTime(date('Y-m-t'));
            $weeks = [];
            $weekNumbers = [];
            $current = clone $first;
            while ($current <= $last) {
                $w = (int)$current->format('W');
                if (!in_array($w, $weekNumbers)) {
                    $weekNumbers[] = $w;
                }
                $current->modify('+1 week');
            }
            foreach ($weekNumbers as $i => $w) {
                $weeks[] = 'Week ' . ($i + 1);
            }

            // Match DB results to week labels
            $profitByWeek = array_fill_keys($weeks, 0);
            foreach ($result as $row) {
                $index = array_search((int)$row['week_num'], $weekNumbers);
                if ($index !== false) {
                    $profitByWeek[$weeks[$index]] = (float)$row['profit'];
                }
            }

            $final = [];
            foreach ($weeks as $w) {
                $final[] = ['label' => $w, 'profit' => $profitByWeek[$w]];
            }
            return $final;
        }

        return $result;
    }
    public function getSalesByCashier()
    {
        $stmt = $this->conn->prepare("CALL GetSalesByCashier()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
