<?php
require_once __DIR__ . '/../includes/db_product.php';

if (isset($_GET['q'])) {
    $q = mysqli_real_escape_string($conn, trim($_GET['q']));
    $cat = isset($_GET['cat']) ? intval($_GET['cat']) : 0;
    
    if (empty($q)) {
        echo json_encode([]);
        exit();
    }
    
    $sql = "SELECT id, name, price, image_id FROM products WHERE status = 'hiện' AND name LIKE '%$q%'";
    if ($cat > 0) {
        $sql .= " AND category_id = $cat";
    }
    $sql .= " LIMIT 5";
    
    $res = mysqli_query($conn, $sql);
    $results = [];
    while($row = mysqli_fetch_assoc($res)) {
        $img = !empty($row['image_id']) ? htmlspecialchars($row['image_id']) : "default-manga.jpg";
        $results[] = [
            'id' => $row['id'],
            'name' => htmlspecialchars($row['name']),
            'price' => number_format((float)$row['price'], 0, ',', '.'),
            'image' => $img
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($results);
}
?>
