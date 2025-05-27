<?php
class UnitModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($name, $abbreviation)
    {
        $stmt = $this->conn->prepare("CALL CreateUnit(:name, :abbreviation)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':abbreviation', $abbreviation);
        return $stmt->execute();
    }

    public function getAll()
    {
        $stmt = $this->conn->prepare("CALL GetAllUnits()");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($unit_id)
    {
        $stmt = $this->conn->prepare("CALL GetUnitById(:unit_id)");
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($unit_id, $name, $abbreviation)
    {
        $stmt = $this->conn->prepare("CALL UpdateUnit(:unit_id, :name, :abbreviation)");
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':abbreviation', $abbreviation);
        return $stmt->execute();
    }

    public function delete($unit_id)
    {
        $stmt = $this->conn->prepare("CALL DeleteUnit(:unit_id)");
        $stmt->bindParam(':unit_id', $unit_id);
        return $stmt->execute();
    }
}
