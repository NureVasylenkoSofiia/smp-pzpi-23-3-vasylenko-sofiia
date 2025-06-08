<?php

// Масив товарів
$products = [
    1 => ['name' => 'Молоко пастеризоване', 'price' => 12],
    2 => ['name' => 'Хліб чорний', 'price' => 9],
    3 => ['name' => 'Сир білий', 'price' => 21],
    4 => ['name' => 'Сметана 20%', 'price' => 25],
    5 => ['name' => 'Кефір 1%', 'price' => 19],
    6 => ['name' => 'Вода газована', 'price' => 18],
    7 => ['name' => 'Печиво "Весна"', 'price' => 14],
];

$cart = [];
$userName = null;
$userAge = null;

function showMenu() {
    echo "################################\n";
    echo "# ПРОДОВОЛЬЧИЙ МАГАЗИН \"ВЕСНА\" #\n";
    echo "################################\n";
    echo "1 Вибрати товари\n";
    echo "2 Отримати підсумковий рахунок\n";
    echo "3 Налаштувати свій профіль\n";
    echo "0 Вийти з програми\n";
    echo "Введіть команду: ";
}

function getInput(): string {
    return trim(fgets(STDIN));
}

function showProducts($products) {
    echo "№  НАЗВА                 ЦІНА\n";
    foreach ($products as $id => $product) {
        printf("%-2d %-21s %4d\n", $id, $product['name'], $product['price']);
    }
    echo "   -----------\n";
    echo "0  ПОВЕРНУТИСЯ\n";
    echo "Виберіть товар: ";
}

function showCart($cart, $products) {
    if (empty($cart)) {
        echo "КОШИК ПОРОЖНІЙ\n";
        return;
    }
    echo "У КОШИКУ:\nНАЗВА                КІЛЬКІСТЬ\n";
    foreach ($cart as $id => $qty) {
        printf("%-20s %d\n", $products[$id]['name'], $qty);
    }
}

function showReceipt($cart, $products) {
    if (empty($cart)) {
        echo "КОШИК ПОРОЖНІЙ\n";
        return;
    }
    echo "№  НАЗВА                 ЦІНА  КІЛЬКІСТЬ  ВАРТІСТЬ\n";
    $i = 1;
    $total = 0;
    foreach ($cart as $id => $qty) {
        $price = $products[$id]['price'];
        $cost = $price * $qty;
        printf("%-2d %-21s %5d %9d %9d\n", $i++, $products[$id]['name'], $price, $qty, $cost);
        $total += $cost;
    }
    echo "РАЗОМ ДО CПЛАТИ: $total\n";
}

function setupProfile(&$userName, &$userAge) {
    do {
        echo "Ваше імʼя: ";
        $userName = trim(fgets(STDIN));
    } while (!preg_match('/\p{L}/u', $userName));

    do {
        echo "Ваш вік: ";
        $userAge = (int)trim(fgets(STDIN));
    } while ($userAge < 7 || $userAge > 150);

    echo "Дані профілю оновлено\n";
}

do {
    showMenu();
    $command = getInput();

    switch ($command) {
        case '1':
            do {
                showProducts($products);
                $choice = (int)getInput();
                if ($choice === 0) break;

                if (!isset($products[$choice])) {
                    echo "ПОМИЛКА! ВКАЗАНО НЕПРАВИЛЬНИЙ НОМЕР ТОВАРУ\n";
                    continue;
                }

                echo "Вибрано: {$products[$choice]['name']}\n";
                echo "Введіть кількість, штук: ";
                $qty = (int)getInput();

                if ($qty < 0 || $qty >= 100) {
                    echo "ПОМИЛКА! НЕВІРНА КІЛЬКІСТЬ\n";
                    continue;
                }

                if ($qty === 0) {
                    unset($cart[$choice]);
                    echo "ВИДАЛЯЮ З КОШИКА\n";
                } else {
                    $cart[$choice] = $qty;
                }

                showCart($cart, $products);

            } while (true);
            break;

        case '2':
            showReceipt($cart, $products);
            break;

        case '3':
            setupProfile($userName, $userAge);
            break;

        case '0':
            echo "До побачення!\n";
            exit;

        default:
            echo "ПОМИЛКА! Введіть правильну команду\n";
    }

} while (true);
