<?php
session_start();

require_once "../config/database.php";
require_once "../models/UnitModel.php";

$db = new Database();
$conn = $db->getConnection();
$unit = new UnitModel($conn);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $name = trim($_POST['name'] ?? '');
        $abbreviation = trim($_POST['abbreviation'] ?? '');

        if (empty($name)) {
            $_SESSION['unit_error'] = "Unit name is required.";
        } else {
            if ($unit->create($name, $abbreviation)) {
                $_SESSION['success'] = "Unit added successfully.";
            } else {
                $_SESSION['unit_error'] = "Failed to add unit.";
            }
        }

        $currentSection = $_POST['current_section'] ?? 'dashboard';
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=products");
        exit;

    case 'update':
        $id = intval($_POST['editUnitId'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $abbreviation = trim($_POST['abbreviation'] ?? '');

        if ($id && $name) {
            if ($unit->update($id, $name, $abbreviation)) {
                $_SESSION['success'] = "Unit updated successfully.";
            } else {
                $_SESSION['unit_error'] = "Failed to update unit.";
            }
        } else {
            $_SESSION['unit_error'] = "Please provide a valid unit name.";
        }

        $currentSection = $_POST['current_section'] ?? 'units';
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=products");
        exit;

    case 'delete':
        $id = intval($_GET['id'] ?? 0);
        if ($id) {
            if ($unit->delete($id)) {
                $_SESSION['success'] = "Unit deleted successfully.";
            } else {
                $_SESSION['unit_error'] = "Failed to delete unit.";
            }
        }

        $currentSection = $_POST['current_section'] ?? 'units';
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=products");
        exit;
}
