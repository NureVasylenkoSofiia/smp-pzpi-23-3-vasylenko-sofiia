<?php
session_start();
if (!isset($_SESSION['login'])) {
    http_response_code(403);
    echo '403 Forbidden – Потрібна авторизація.';
    exit;
}


$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<h2>Ваш кошик</h2>

<?php if (empty($cart)): ?>
    <p>Кошик порожній.</p>
<?php else: ?>
    <table>
        <tr>
            <th>Назва</th>
            <th>Ціна</th>
            <th>Кількість</th>
            <th>Сума</th>
            <th>Дії</th>
        </tr>
        <?php foreach ($cart as $item): 
            $sum = $item['price'] * $item['count'];
            $total += $sum;
        ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['price'] ?> грн</td>
                <td><?= $item['count'] ?></td>
                <td><?= $sum ?> грн</td>
                <td>
                    <a class="button danger" href="remove_from_cart.php?id=<?= $item['id'] ?>">Видалити</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3"><strong>Загалом:</strong></td>
            <td colspan="2"><strong><?= $total ?> грн</strong></td>
        </tr>
    </table>
    <br>
    <a class="button danger" href="clear_cart.php">Очистити кошик</a>
<?php endif; ?>