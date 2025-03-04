<?php
require_once "config.php";

$term = isset($_GET['term']) ? $_GET['term'] : '';

$sql = "SELECT DISTINCT city FROM articles WHERE city LIKE ? ORDER BY city ASC";
$stmt = $link->prepare($sql);
$term = '%' . $term . '%';
$stmt->bind_param("s", $term);
$stmt->execute();
$result = $stmt->get_result();

$cities = [];
while ($row = $result->fetch_assoc()) {
    $cities[] = $row['city'];
}

echo json_encode($cities);
$stmt->close();
?>
