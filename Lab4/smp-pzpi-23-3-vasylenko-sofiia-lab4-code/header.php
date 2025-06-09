<?php session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Магазин Весна</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav>
        <a href="main.php?page=main">Головна</a>
        <a href="main.php?page=products_content">Товари</a>
        <a href="main.php?page=basket_content">Кошик</a>
        <?php if (isset($_SESSION['login'])): ?>
            <a href="main.php?page=profile">Профіль</a>
            <a href="logout.php">Вийти</a>
        <?php else: ?>
            <a href="main.php?page=login">Увійти</a>
        <?php endif; ?>
    </nav>
</header>
<main>
