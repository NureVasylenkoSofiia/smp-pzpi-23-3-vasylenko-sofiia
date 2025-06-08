<?php
session_start();
include 'data.php';

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
