<?php
require_once __DIR__ . '/../config/DB.php';
$pdo = DB::getConnection();
 


$mysqli = new mysqli("localhost", "root", "", "shirtastic_db");

if ($mysqli->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connection failed"]);
    exit;
}

$result = $mysqli->query("SELECT id, name FROM categories");

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

echo json_encode(["status" => "ok", "categories" => $categories]);
$mysqli->close();
