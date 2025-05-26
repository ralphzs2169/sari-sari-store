<?php
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo 'Connection Successful';
} else {
    echo 'Connection Failed';
}
