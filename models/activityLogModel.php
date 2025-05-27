<?php
class ActivityLogModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($admin_id, $activity_type, $description, $product_id = null)
    {
        $stmt = $this->conn->prepare("CALL CreateActivityLog(:admin_id, :activity_type, :description, :product_id)");
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->bindParam(':activity_type', $activity_type);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("CALL GetAllActivityLogs()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
