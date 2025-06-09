<?php
session_start();
// Підключення до бази
$db = new PDO('sqlite:' . __DIR__ . '/vesna.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Отримання списку товарів
$stmt = $db->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

if (isset($_POST['count']) && is_array($_POST['count'])) {
    foreach ($_POST['count'] as $id => $count) {
        $id = (int)$id;
        $count = (int)$count;
        if ($count > 0 && isset($products[$id])) {
            $_SESSION['cart'][$id] = [
                'id' => $id,
                'name' => $products[$id]['name'],
                'price' => $products[$id]['price'],
                'count' => $count,
            ];
        } elseif (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]); 
        }
    }
}

header('Location: basket.php');
exit;
