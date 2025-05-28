<?php
class ActivityLogModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($activity_type, $description)
    {
        $stmt = $this->conn->prepare("CALL CreateActivityLog(:activity_type, :description)");
        $stmt->bindParam(':activity_type', $activity_type);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("CALL GetAllActivityLogs()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
