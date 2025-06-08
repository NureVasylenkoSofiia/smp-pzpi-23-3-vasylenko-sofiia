<?php
session_start();
include 'data.php';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Магазин - Товари</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Шапка -->
    <header>
        <nav>
            <a href="index.php">Головна</a>
            <a href="products.php">Товари</a>
            <a href="basket.php">Кошик</a>
        </nav>
    </header>

    <!-- Тіло -->
    <main>
        <h2>Список товарів</h2>
        <form method="POST" action="add_to_cart.php">
            <table>
                <tr>
                    <th>Назва</th>
                    <th>Ціна</th>
                    <th>Кількість</th>
                </tr>
                <?php foreach ($products as $id => $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= $product['price'] ?> грн</td>
                        <td>
                            <input type="number" name="count[<?= $id ?>]" min="0" value="0">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <br>
            <input type="submit" class="button" value="Купити">
        </form>
    </main>

    <!-- Підвал -->
    <footer>
        <p>&copy; <?= date("Y") ?> Магазин Весна</p>
    </footer>
</body>
</html>