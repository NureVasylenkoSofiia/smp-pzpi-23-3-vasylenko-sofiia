<?php include 'data.php'; ?>

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