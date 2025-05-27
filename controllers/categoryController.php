<?php
session_start();

require_once "../config/database.php";
require_once "../models/CategoryModel.php";

$db = new Database();
$conn = $db->getConnection();
$category = new CategoryModel($conn);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'create':
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $_SESSION['category_error'] = "Category name is required.";
        } else {
            $category->create($name, $description);
            $_SESSION['success'] = "Category added successfully.";
        }
        $currentSection = $_POST['current_section'] ?? 'dashboard';
        header("Location: /sari-sari-store/views/adminPanel/index.php?section=" . urlencode($currentSection));
        exit;


    case 'update':
        $id = intval($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($id && $name) {
            $category->update($id, $name, $description);
            $_SESSION['success'] = "Category updated.";
        } else {
            $_SESSION['category_error'] = "Please provide a valid category name.";
        }
        header("Location: ../views/categories.php");
        exit;

    case 'delete':
        $id = intval($_GET['id'] ?? 0);
        if ($id) {
            $category->delete($id);
            $_SESSION['success'] = "Category deleted.";
        }
        header("Location: ../views/categories.php");
        exit;
}
