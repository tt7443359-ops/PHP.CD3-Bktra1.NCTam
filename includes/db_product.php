<?php
require_once __DIR__ . '/db.php';

function getAllProducts($conn) {
    // Lấy thêm thông tin Category
    $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getActiveProducts($conn, $category_id = null, $search = null) {
    // Dành cho trang khách
    $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 'hiện'";
    
    if ($category_id) {
        $category_id = intval($category_id);
        $sql .= " AND p.category_id = $category_id";
    }
    if ($search) {
        $search = mysqli_real_escape_string($conn, $search);
        $sql .= " AND p.name LIKE '%$search%'";
    }
    $sql .= " ORDER BY p.id DESC";
    
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getCategories($conn) {
    $sql = "SELECT * FROM categories ORDER BY id ASC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>