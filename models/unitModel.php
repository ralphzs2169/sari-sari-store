<?php
// Check if admin_id is set in the session. If not, redirect to login page.
if (!isset($_SESSION['admin_id'])) {
    header('Location: /sari-sari-store/views/login.php');
    exit();
}

class UnitModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($name, $abbreviation)
    {
        try {
            $this->conn->beginTransaction();

            // Create unit
            $stmt = $this->conn->prepare("CALL CreateUnit(:name, :abbreviation)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':abbreviation', $abbreviation);
            $stmt->execute();

            // Log activity
            $desc = "$name ($abbreviation) unit added by " . $_SESSION['admin_username'];
            $logStmt = $this->conn->prepare("CALL CreateActivityLog(:activity_type, :description)");
            $activity_type = 'unit_created';
            $logStmt->bindParam(':activity_type', $activity_type);
            $logStmt->bindParam(':description', $desc);
            $logStmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Create unit failed: " . $e->getMessage());
            return false;
        }
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

    public function getUnitNameById($unit_id)
    {
        $stmt = $this->conn->prepare("CALL GetUnitById(:unit_id)");
        $stmt->bindParam(':unit_id', $unit_id, PDO::PARAM_INT);
        $stmt->execute();
        $unit = $stmt->fetch(PDO::FETCH_ASSOC);
        return $unit ? $unit['name'] : 'Unknown Unit';
    }

    public function update($unit_id, $name, $abbreviation)
    {
        try {
            $this->conn->beginTransaction();

            $original = $this->getById($unit_id);
            if (!$original) throw new Exception("Unit not found.");

            $stmt = $this->conn->prepare("CALL UpdateUnit(:unit_id, :name, :abbreviation)");
            $stmt->bindParam(':unit_id', $unit_id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':abbreviation', $abbreviation);
            $stmt->execute();

            // Activity logging
            if ($original['name'] !== $name) {
                $logStmt = $this->conn->prepare("CALL CreateActivityLog(:type, :desc)");
                $type = 'unit_name_updated';
                $desc = "Unit renamed from '{$original['name']}' to '$name' by " . $_SESSION['admin_username'];
                $logStmt->bindParam(':type', $type);
                $logStmt->bindParam(':desc', $desc);
                $logStmt->execute();
            }

            if ($original['abbreviation'] !== $abbreviation) {
                $logStmt = $this->conn->prepare("CALL CreateActivityLog(:type, :desc)");
                $type = 'unit_abbreviation_updated';
                $desc = "$name: Abbreviation changed from '{$original['abbreviation']}' to '$abbreviation' by " . $_SESSION['admin_username'];
                $logStmt->bindParam(':type', $type);
                $logStmt->bindParam(':desc', $desc);
                $logStmt->execute();
            }

            // General update log
            $logStmt = $this->conn->prepare("CALL CreateActivityLog(:type, :desc)");
            $type = 'unit_updated';
            $desc = "$name unit updated by " . $_SESSION['admin_username'];
            $logStmt->bindParam(':type', $type);
            $logStmt->bindParam(':desc', $desc);
            $logStmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Unit update failed: " . $e->getMessage());
            return false;
        }
    }


    public function delete($unit_id)
    {
        try {
            $this->conn->beginTransaction();

            $original = $this->getById($unit_id);
            if (!$original) {
                throw new Exception("Unit not found");
            }

            // Log activity BEFORE deletion
            $desc = "{$original['name']} ({$original['abbreviation']}) unit deleted by " . $_SESSION['admin_username'];
            $logStmt = $this->conn->prepare("CALL CreateActivityLog(:activity_type, :description)");
            $activity_type = 'unit_deleted';
            $logStmt->bindParam(':activity_type', $activity_type);
            $logStmt->bindParam(':description', $desc);
            $logStmt->execute();

            // Delete unit
            $stmt = $this->conn->prepare("CALL DeleteUnit(:unit_id)");
            $stmt->bindParam(':unit_id', $unit_id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Delete unit failed: " . $e->getMessage());
            return false;
        }
    }
}
