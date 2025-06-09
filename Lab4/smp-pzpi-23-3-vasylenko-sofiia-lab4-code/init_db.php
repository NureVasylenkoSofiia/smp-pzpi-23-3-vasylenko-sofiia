<?php
$dbFile = __DIR__ . '/vesna.sqlite';

if (file_exists($dbFile)) {
    echo "База вже існує: {$dbFile}\n";
    exit;
}

try {
    $db = new PDO('sqlite:' . $dbFile);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            price REAL NOT NULL,
            image TEXT DEFAULT ''
        );
    ");

    $products = [
        ['name' => 'Молоко пастеризоване', 'price' => 12, 'image' => 'milk.png'],
        ['name' => 'Хліб чорний', 'price' => 9, 'image' => 'bread.png'],
        ['name' => 'Сир білий', 'price' => 21, 'image' => 'cheese.png'],
        ['name' => 'Сметана 20%', 'price' => 25, 'image' => 'sour-cream.png'],
        ['name' => 'Кефір 1%', 'price' => 19, 'image' => 'kefir.png'],
        ['name' => 'Вода газована', 'price' => 18, 'image' => 'water.png'],
        ['name' => 'Печиво "Весна"', 'price' => 14, 'image' => 'cookies.png'],
    ];

    $stmt = $db->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
    foreach ($products as $p) {
        $stmt->execute([$p['name'], $p['price'], $p['image']]);
    }

    echo "Базу створено та заповнено: {$dbFile}\n";
} catch (PDOException $e) {
    echo "Помилка: " . $e->getMessage() . "\n";
}
